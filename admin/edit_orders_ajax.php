<?php
  /*
  $Id: edit_orders_ajax.php v5.0.7 11/18/2009 surfalot Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  Released under the GNU General Public License

  For Order Editor support or to post bug reports, feature requests, etc, please visit the Order Editor support thread:
  http://forums.oscommerce.com/index.php?showtopic=54032

  */

  require('includes/application_top.php');

  // output a response header
  header('Content-type: text/html; charset=' . CHARSET . '');

  // include the appropriate functions & classes
  include('order_editor/functions.php');
  include('order_editor/cart.php');
  include('order_editor/order.php');
  include('order_editor/shipping.php');
  include(DIR_WS_LANGUAGES . $language. '/' . 'edit_orders.php');


  // Include currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  //$action
  //all variables are sent by $_GET only or by $_POST only, never together
  if (sizeof($_GET) > 0) {
    $action = $_GET['action'];
    $oID = (isset($_GET['oID']) ? tep_db_prepare_input($_GET['oID']) : '');
    $pID = (isset($_GET['pID']) ? tep_db_prepare_input($_GET['pID']) : '');

  } elseif (sizeof($_POST) > 0) {
    $action = $_POST['action'];
    $oID = (isset($_POST['oID']) ? tep_db_prepare_input($_POST['oID']) : '');
   }

  //1.  Update most the orders table
  if ($action == 'update_order_field') {
   tep_db_query("UPDATE " . TABLE_ORDERS . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_id = '" . $oID . "'");
    //generate responseText
    echo $_GET['field'];
  }

  //2.  Update the orders_products table for qty, tax, name, or model
  if ($action == 'update_product_field') {

    if ($_GET['field'] == 'products_quantity') {
      // Update Inventory Quantity
      $order_query = tep_db_query("
      SELECT products_id, products_quantity
      FROM " . TABLE_ORDERS_PRODUCTS . "
      WHERE orders_id = '" . $oID . "'
      AND orders_products_id = '" . $pID . "'");
      $orders_product_info = tep_db_fetch_array($order_query);

      // stock check

      if ($_GET['new_value'] != $orders_product_info['products_quantity']){
      $quantity_difference = ($_GET['new_value'] - $orders_product_info['products_quantity']);
        if (STOCK_LIMITED == 'true'){
            tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET
          products_quantity = products_quantity - " . $quantity_difference . ",
          products_ordered = products_ordered + " . $quantity_difference . "
          WHERE products_id = '" . $orders_product_info['products_id'] . "'");
// QT Pro Addon BOF
          if (ORDER_EDITOR_USE_QTPRO == 'true') {
            $attrib_q = tep_db_query("select distinct op.products_id, po.products_options_id, pov.products_options_values_id
                                      from products_options po, products_options_values pov, products_options_values_to_products_options po2pov, orders_products_attributes opa, orders_products op
                                      where op.orders_id = '" . $oID . "'
                                      and op.orders_products_id = '" . $pID . "'
                                      and products_options_values_name = opa.products_options_values
                                      and pov.products_options_values_id = po2pov.products_options_values_id
                                      and po.products_options_id = po2pov.products_options_id
                                      and products_options_name = opa.products_options");
            while($attrib_set = tep_db_fetch_array($attrib_q)) {
              // corresponding to each option find the attribute ids ( opts and values id )
              $products_stock_attributes[] = $attrib_set['products_options_id'].'-'.$attrib_set['products_options_values_id'];
            }
            sort($products_stock_attributes, SORT_NUMERIC); // Same sort as QT Pro stock
            $products_stock_attributes = implode($products_stock_attributes, ',');
             // update the stock
            tep_db_query("update ".TABLE_PRODUCTS_STOCK." set products_stock_quantity = products_stock_quantity - ".$quantity_difference . " where products_id= '" . $orders_product_info['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
          }
// QT Pro Addon EOF
          } else {
          tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
          products_ordered = products_ordered + " . $quantity_difference . "
          WHERE products_id = '" . $orders_product_info['products_id'] . "'");
        } //end if (STOCK_LIMITED == 'true'){
      } //end if ($_GET['new_value'] != $orders_product_info['products_quantity']){
    }//end if ($_GET['field'] = 'products_quantity'

    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_products_id = '" . $pID . "' AND orders_id = '" . $oID . "'");

    //generate responseText
    echo $_GET['field'];

  }

  //3.  Update the orders_products table for price and final_price (interdependent values)
  if ($action == 'update_product_value_field') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET products_price = '" . tep_db_input(tep_db_prepare_input($_GET['price'])) . "', final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . "' WHERE orders_products_id = '" . $pID . "' AND orders_id = '" . $oID . "'");

    //generate responseText
    echo TABLE_ORDERS_PRODUCTS;

  }

    //4.  Update the orders_products_attributes table
  if ($action == 'update_attributes_field') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_products_attributes_id = '" . $_GET['aid'] . "' AND orders_products_id = '" . $pID . "' AND orders_id = '" . $oID . "'");

    if (isset($_GET['final_price'])) {
      tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . "' WHERE orders_products_id = '" . $pID . "' AND orders_id = '" . $oID . "'");
    }
    //generate responseText
    echo $_GET['field'];
  }

    //5.  Update the orders_products_download table
  if ($action == 'update_downloads') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " SET " . $_GET['field'] . " = '" . tep_db_input(tep_db_prepare_input($_GET['new_value'])) . "' WHERE orders_products_download_id = '" . $_GET['did'] . "' AND orders_products_id = '" . $pID . "' AND orders_id = '" . $oID . "'");

   //generate responseText
    echo $_GET['field'];

  }

  //6. Update the currency of the order
  if ($action == 'update_currency') {
      tep_db_query("UPDATE " . TABLE_ORDERS . " SET currency = '" . tep_db_input(tep_db_prepare_input($_GET['currency'])) . "', currency_value = '" . tep_db_input(tep_db_prepare_input($_GET['currency_value'])) . "' WHERE orders_id = '" . $oID . "'");

     //generate responseText
    echo $_GET['currency'];

  }//end if ($action == 'update_currency') {


  //7.  Update most any field in the orders_products table
  if ($action == 'delete_product_field') {

    //  Update Inventory Quantity
    $order_query = tep_db_query("SELECT products_id, products_quantity
                                 FROM " . TABLE_ORDERS_PRODUCTS . 
                                " WHERE orders_id = '" . $oID . 
                                "' AND orders_products_id = '" . $pID . "'");
    $order_products = tep_db_fetch_array($order_query);

             //update quantities first
    if (STOCK_LIMITED == 'true'){
      tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET
                    products_quantity = products_quantity + " . $order_products['products_quantity'] . ",
                    products_ordered = products_ordered - " . $order_products['products_quantity'] . "
                    WHERE products_id = '" . (int)$order_products['products_id'] . "'");
// QT Pro Addon BOF
               if (ORDER_EDITOR_USE_QTPRO == 'true') {
                 $attrib_q = tep_db_query("SELECT DISTINCT op.products_id, po.products_options_id, pov.products_options_values_id
                                           FROM products_options po, products_options_values pov, products_options_values_to_products_options po2pov, orders_products_attributes opa, orders_products op
                                           WHERE op.orders_id = '" . $oID . "'
                                           AND op.orders_products_id = '" . $pID . "'
                                           AND products_options_values_name = opa.products_options_values
                                           AND pov.products_options_values_id = po2pov.products_options_values_id
                                           AND po.products_options_id = po2pov.products_options_id
                                           AND products_options_name = opa.products_options");
                  while($attrib_set = tep_db_fetch_array($attrib_q)) {
                    $products_stock_attributes[] = $attrib_set['products_options_id'].'-'.$attrib_set['products_options_values_id'];
                  }
                  sort($products_stock_attributes, SORT_NUMERIC); // Same sort as QT Pro stock
                  $products_stock_attributes = implode($products_stock_attributes, ',');
                  // update the stock
                  tep_db_query("UPDATE " . TABLE_PRODUCTS_STOCK . " SET products_stock_quantity = products_stock_quantity + ".$order['products_quantity'] . " WHERE products_id= '" . (int)$order_products['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
                }
// QT Pro Addon EOF
              } else {
                tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
                               products_ordered = products_ordered - " . $order_products['products_quantity'] . "
                               WHERE products_id = '" . (int)$order_products['products_id'] . "'");
              }

              tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . "
                            WHERE orders_id = '" . $oID . "'
                            AND orders_products_id = '" . $pID . "'");

              tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                            WHERE orders_id = '" . $oID . "'
                            AND orders_products_id = '" . $pID . "'");

              tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
                            WHERE orders_id = '" . $oID . "'
                            AND orders_products_id = '" . $pID . "'");

      //generate responseText
    echo TABLE_ORDERS_PRODUCTS;

  }

  //8. Update the orders_status_history table
  if ($action == 'delete_comment') {
    tep_db_query("DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_status_history_id = '" . $_GET['cID'] . "' AND orders_id = '" . $oID . "'");
    //generate responseText
    echo TABLE_ORDERS_STATUS_HISTORY;
  }

  //9. Update the orders_status_history table
  if ($action == 'update_comment') {
    tep_db_query("UPDATE " . TABLE_ORDERS_STATUS_HISTORY . " SET comments = '" . oe_iconv($_GET['comment']) . "' WHERE orders_status_history_id = '" . $_GET['cID'] . "' AND orders_id = '" . $oID . "'");
    //generate responseText
    echo TABLE_ORDERS_STATUS_HISTORY;
  }


  //10. Reload the shipping and order totals block
    if ($action == 'reload_totals') {

// reload_totals.php begins
      $shipping = array();
      if (is_array($_POST['update_totals'])) {
        foreach($_POST['update_totals'] as $total_index => $total_details) {
          extract($total_details, EXTR_PREFIX_ALL, "ot");
          if ($ot_class == "ot_shipping") {

            $shipping['cost'] = $ot_value;
            $shipping['title'] = $ot_title;
            $shipping['id'] = $ot_id;

          } // end if ($ot_class == "ot_shipping")
        } //end foreach
      } //end if is_array

      if (tep_not_null($shipping['id'])) {
        tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $shipping['id'] . "' WHERE orders_id = '" . $oID . "'");
      }

      $order = new manualOrder($oID);
      $order->adjust_zones();

      $cart = new manualCart();
      $cart->restore_contents($oID);
      $total_count = $cart->count_contents();
      $total_weight = $cart->show_weight();

      // Get the shipping quotes- if we don't have shipping quotes shipping tax calculation can't happen
      $shipping_modules = new shipping;
      $shipping_quotes = $shipping_modules->quote();

        if (DISPLAY_PRICE_WITH_TAX == 'true') {//extract the base shipping cost or the ot_shipping module will add tax to it again
          $module = substr($GLOBALS['shipping']['id'], 0, strpos($GLOBALS['shipping']['id'], '_'));
          $tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $order->info['total'] -= ( $order->info['shipping_cost'] - ($order->info['shipping_cost'] / (1 + ($tax /100))) );
          $order->info['shipping_cost'] = ($order->info['shipping_cost'] / (1 + ($tax /100)));
        }

        //this is where we call the order total modules
        require( 'order_editor/order_total.php');
        $order_total_modules = new order_total();
        $order_totals = $order_total_modules->process();

        $current_ot_totals_array = array();
        $current_ot_titles_array = array();
        $written_ot_totals_array = array();
        $written_ot_titles_array = array();
        //how many weird arrays can I make today?
        $current_ot_totals_query = tep_db_query("select class, title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $oID . "' order by sort_order");
        while ($current_ot_totals = tep_db_fetch_array($current_ot_totals_query)) {
          $current_ot_totals_array[] = $current_ot_totals['class'];
          $current_ot_titles_array[] = $current_ot_totals['title'];
        }

        tep_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . $oID . "'");

        $j=1; //giving something a sort order of 0 ain't my bag baby
        $new_order_totals = array();

        if (is_array($_POST['update_totals'])) { //1
          foreach($_POST['update_totals'] as $total_index => $total_details) { //2
            extract($total_details, EXTR_PREFIX_ALL, "ot");
            if (!strstr($ot_class, 'ot_custom')) { //3
             for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //4

        if ($order_totals[$i]['code'] == 'ot_tax') { //5
        $new_ot_total = ((in_array($order_totals[$i]['title'], $current_ot_titles_array)) ? false : true);
        } else { //within 5
        $new_ot_total = ((in_array($order_totals[$i]['code'], $current_ot_totals_array)) ? false : true);
        }  //end 5 if ($order_totals[$i]['code'] == 'ot_tax')

        if ( ( ($order_totals[$i]['code'] == 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) && ($order_totals[$i]['title'] == $ot_title) ) || ( ($order_totals[$i]['code'] != 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) ) ) { //6
        //only good for components that show up in the $order_totals array

        if ($ot_title != '') { //7
                  $new_order_totals[] = array('title' => $ot_title,
                                              'text' => (($ot_class != 'ot_total') ? $order_totals[$i]['text'] : '<b>' . $currencies->format($order->info['total'], true, $order->info['currency'], $order->info['currency_value']) . '</b>'),
                                              'value' => (($order_totals[$i]['code'] != 'ot_total') ? $order_totals[$i]['value'] : $order->info['total']),
                                              'code' => $order_totals[$i]['code'],
                                              'sort_order' => $j);
                $written_ot_totals_array[] = $ot_class;
                $written_ot_titles_array[] = $ot_title;
                $j++;
                } else { //within 7

                  $order->info['total'] += ($ot_value*(-1));
                  $written_ot_totals_array[] = $ot_class;
                  $written_ot_titles_array[] = $ot_title;

                } //end 7

              } elseif ( ($new_ot_total) && (!in_array($order_totals[$i]['title'], $current_ot_titles_array)) ) { //within 6
                $new_order_totals[] = array('title' => $order_totals[$i]['title'],
                                            'text' => $order_totals[$i]['text'],
                                            'value' => $order_totals[$i]['value'],
                                            'code' => $order_totals[$i]['code'],
                                            'sort_order' => $j);
                $current_ot_totals_array[] = $order_totals[$i]['code'];
                $current_ot_titles_array[] = $order_totals[$i]['title'];
                $written_ot_totals_array[] = $ot_class;
                $written_ot_titles_array[] = $ot_title;
                $j++;
                // echo $order_totals[$i]['code'] . "<br>"; for debugging- use of this results in errors

              } elseif ($new_ot_total) { //also within 6
                $order->info['total'] += ($order_totals[$i]['value']*(-1));
                $current_ot_totals_array[] = $order_totals[$i]['code'];
                $written_ot_totals_array[] = $ot_class;
                $written_ot_titles_array[] = $ot_title;
              }//end 6
           }//end 4
         } elseif ( (tep_not_null($ot_value)) && (tep_not_null($ot_title)) ) { // this modifies if (!strstr($ot_class, 'ot_custom')) { //3
            $new_order_totals[] = array('title' => $ot_title,
                     'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                                        'value' => $ot_value,
                                        'code' => 'ot_custom_' . $j,
                                        'sort_order' => $j);
            $order->info['total'] += $ot_value;

            $written_ot_totals_array[] = $ot_class;
            $written_ot_titles_array[] = $ot_title;
            $j++;
          } //end 3

            //save ot_skippy from certain annihilation
             if ( (!in_array($ot_class, $written_ot_totals_array)) && (!in_array($ot_title, $written_ot_titles_array)) && (tep_not_null($ot_value)) && (tep_not_null($ot_title)) && ($ot_class != 'ot_tax') && ($ot_class != 'ot_loworderfee') ) { //7
            //this is supposed to catch the oddball components that don't show up in $order_totals
            $new_order_totals[] = array(
                            'title' => $ot_title,
                            'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                            'value' => $ot_value,
                            'code' => $ot_class,
                            'sort_order' => $j);
               //$current_ot_totals_array[] = $order_totals[$i]['code'];
                //$current_ot_titles_array[] = $order_totals[$i]['title'];
                $written_ot_totals_array[] = $ot_class;
                $written_ot_titles_array[] = $ot_title;
                $j++;

          } //end 7
        } //end 2
      } else {//within 1
      // $_POST['update_totals'] is not an array => write in all order total components that have been generated by the sundry modules
       for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //8
         $new_order_totals[] = array('title' => $order_totals[$i]['title'],
                                     'text' => $order_totals[$i]['text'],
                                     'value' => $order_totals[$i]['value'],
                                     'code' => $order_totals[$i]['code'],
                                     'sort_order' => $j);
                $j++;

      } //end 8

    } //end if (is_array($_POST['update_totals'])) { //1

        for ($i=0, $n=sizeof($new_order_totals); $i<$n; $i++) {
          $sql_data_array = array('orders_id' => $oID,
                                  'title' => oe_iconv($new_order_totals[$i]['title']),
                                  'text' => $new_order_totals[$i]['text'],
                                  'value' => $new_order_totals[$i]['value'],
                                  'class' => $new_order_totals[$i]['code'],
                                  'sort_order' => $new_order_totals[$i]['sort_order']);
          tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
        }


        $order = new manualOrder($oID);
        $shippingKey = $order->adjust_totals($oID);
        $order->adjust_zones();

        $cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();

// reload_totals.php end

      
include ('order_editor/10.php');



    }//end if ($action == 'reload_totals') {


  //11. insert new comments
   if ($action == 'insert_new_comment') {
    //orders status
    $orders_statuses = array();
    $orders_status_array = array();
    $orders_status_query = tep_db_query("SELECT orders_status_id, orders_status_name
                                         FROM " . TABLE_ORDERS_STATUS . "
                                         WHERE language_id = '" . (int)$languages_id . "'");

    while ($orders_status = tep_db_fetch_array($orders_status_query)) {
      $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                                 'text' => $orders_status['orders_status_name']);
      $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
    }

  // UPDATE STATUS HISTORY & SEND EMAIL TO CUSTOMER IF NECESSARY #####
    $check_status_query = tep_db_query("SELECT customers_name, customers_email_address, orders_status, date_purchased
                                        FROM " . TABLE_ORDERS . "
                                        WHERE orders_id = '" . $oID . "'");
    $check_status = tep_db_fetch_array($check_status_query);

    if (($check_status['orders_status'] != $_GET['status']) || (tep_not_null($_GET['comments']))) {
      tep_db_query("UPDATE " . TABLE_ORDERS . " SET
                    orders_status = '" . tep_db_input($_GET['status']) . "',
                    last_modified = now()
                    WHERE orders_id = '" . $oID . "'");

 // Notify Customer ?
      $customer_notified = '0';
      if (isset($_GET['notify']) && ($_GET['notify'] == 'true')) {
        $notify_comments = '';
        if (isset($_GET['notify_comments']) && ($_GET['notify_comments'] == 'true')) {
          $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, oe_iconv($_GET['comments'])) . "\n\n";
        }
        $email = STORE_NAME . "\n" .
         EMAIL_SEPARATOR . "\n" .
         EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" .
         EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" .
         EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" .
         sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$_GET['status']]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);

        tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      $customer_notified = '1';
      }

      tep_db_query("INSERT into " . TABLE_ORDERS_STATUS_HISTORY . "
                    (orders_id, orders_status_id, date_added, customer_notified, comments)
                    values ('" . tep_db_input($oID) . "',
                    '" . tep_db_input($_GET['status']) . "',
                    now(),
                    " . tep_db_input($customer_notified) . ",
                    '" . oe_iconv($_GET['comments'])  . "')");
    }
     include ('order_editor/11.php');
  }  // end if ($action == 'insert_new_comment') {


   //12. insert shipping method when one doesn't already exist
   if ($action == 'insert_shipping') {

    $order = new manualOrder($oID);
    $Query = "INSERT INTO " . TABLE_ORDERS_TOTAL . " SET
             orders_id = '" . $oID . "',
             title = '" . $_GET['title'] . "',
             text = '" . $currencies->format($_GET['value'], true, $order->info['currency'], $order->info['currency_value']) ."',
             value = '" . $_GET['value'] . "',
             class = 'ot_shipping',
             sort_order = '" . $_GET['sort_order'] . "'";
    tep_db_query($Query);

    tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $_GET['id'] . "' WHERE orders_id = '" . $oID . "'");

    $order = new manualOrder($oID);
    $shippingKey = $order->adjust_totals($oID);
    $order->adjust_zones();

    $cart = new manualCart();
    $cart->restore_contents($oID);
    $total_count = $cart->count_contents();
    $total_weight = $cart->show_weight();

  // Get the shipping quotes
    $shipping_modules = new shipping;
    $shipping_quotes = $shipping_modules->quote();

    include ('order_editor/templates/totalsBlock.php');
     
     
  } //end if ($action == 'insert_shipping') {


  //13. new order email

  if ($action == 'new_order_email')  {

    $order = new manualOrder($oID);

        for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    //loop all the products in the order
       $products_ordered_attributes = '';
    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
    $products_ordered_attributes .= "\n\t" . $order->products[$i]['attributes'][$j]['option'] . ' ' . $order->products[$i]['attributes'][$j]['value'];
      }
    }

     $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . $products_model . ' = ' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . $products_ordered_attributes . "\n";
       }

    //Build the email
       $email_order = STORE_NAME . "\n" .
                        EMAIL_SEPARATOR . "\n" .
            EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" .
                        EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" .
                      EMAIL_TEXT_DATE_MODIFIED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";

      $email_order .= EMAIL_TEXT_PRODUCTS . "\n" .
                      EMAIL_SEPARATOR . "\n" .
                      $products_ordered .
                      EMAIL_SEPARATOR . "\n";

    for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
        $email_order .= strip_tags($order->totals[$i]['title']) . ' ' . strip_tags($order->totals[$i]['text']) . "\n";
      }

    if ($order->content_type != 'virtual') {
      $email_order .= "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" .
                      EMAIL_SEPARATOR . "\n" .
            $order->delivery['name'] . "\n";
            if ($order->delivery['company']) {
                      $email_order .= $order->delivery['company'] . "\n";
                      }
    $email_order .= $order->delivery['street_address'] . "\n";
                    if ($order->delivery['suburb']) {
                      $email_order .= $order->delivery['suburb'] . "\n";
                      }
    $email_order .= $order->customer['city'] . "\n";
                    if ($order->delivery['state']) {
                      $email_order .= $order->delivery['state'] . "\n";
                      }
    $email_order .= $order->customer['postcode'] . "\n" .
            $order->delivery['country'] . "\n";
    }

      $email_order .= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
                      EMAIL_SEPARATOR . "\n" .
            $order->billing['name'] . "\n";
            if ($order->billing['company']) {
                      $email_order .= $order->billing['company'] . "\n";
                      }
    $email_order .= $order->billing['street_address'] . "\n";
                    if ($order->billing['suburb']) {
                      $email_order .= $order->billing['suburb'] . "\n";
                      }
    $email_order .= $order->customer['city'] . "\n";
                    if ($order->billing['state']) {
                      $email_order .= $order->billing['state'] . "\n";
                      }
    $email_order .= $order->customer['postcode'] . "\n" .
            $order->billing['country'] . "\n\n";

      $email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" .
                      EMAIL_SEPARATOR . "\n";
      $email_order .= $order->info['payment_method'] . "\n\n";


      //  if ( ($order->info['payment_method'] == ORDER_EDITOR_SEND_INFO_PAYMENT_METHOD) && (EMAIL_TEXT_PAYMENT_INFO) ) {
          //     $email_order .= EMAIL_TEXT_PAYMENT_INFO . "\n\n";
           //   }
       //I'm not entirely sure what the purpose of this is so it is being shelved for now

        if (EMAIL_TEXT_FOOTER) {
          $email_order .= EMAIL_TEXT_FOOTER . "\n\n";
          }

    //code for plain text emails which changes the € sign to EUR, otherwise the email will show ? instead of €
      $email_order = str_replace("€","EUR",$email_order);
    $email_order = str_replace("&nbsp;"," ",$email_order);

    //code which replaces the <br> tags within EMAIL_TEXT_PAYMENT_INFO and EMAIL_TEXT_FOOTER with the proper \n
    $email_order = str_replace("<br>","\n",$email_order);

    //send the email to the customer
    tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

   // send emails to other people as necessary
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
  }

  ?>

  <table>
    <tr>
      <td class="messageStackSuccess">
      <?php echo tep_image(DIR_WS_ICONS . 'success.gif', ICON_SUCCESS) . '&nbsp;' . sprintf(AJAX_SUCCESS_EMAIL_SENT, $order->customer['email_address']); ?>
    </td>
    </tr>
  </table>

<?php
  } //end if ($action == 'new_order_email') {
