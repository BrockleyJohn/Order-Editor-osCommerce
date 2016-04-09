<?php
/*
  $Id: edit_orders.php v5.0.9 08/27/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License

  For Order Editor support or to post bug reports, feature requests, etc, please visit the Order Editor support thread:
  http://forums.oscommerce.com/index.php?showtopic=54032

  Eliminados addons superfluos 31/03/2016 JMC

*/

  require('includes/application_top.php');

    // check for database field shipping_module on table orders. Move later to a module
   if (!tep_db_num_rows(tep_db_query("SHOW COLUMNS FROM orders LIKE 'shipping_module'"))) {
    tep_db_query ("alter table orders add shipping_module varchar(255) NULL");
    // este campo será para saber si el pedido ha sido alterado manualmente
    // se incluirá un boton para volver a la versión original que comprobará ese campo
    // habrá que crear tablas nuevas para el backup: oe_orders, oe_orders_products...
   }

  // require the appropriate functions & classes
  require('order_editor/functions.php');
  require('order_editor/manualcart.php');
  require('order_editor/manualorder.php');
  require('order_editor/shipping.php');
  require(DIR_WS_LANGUAGES . $language. '/' . 'edit_orders.php');

  // require currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

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

$action = (isset($_GET['action']) ? $_GET['action'] : 'edit');

//        if (!$oID) {
  if (isset($action)) {
    switch ($action) {
    ////
    // Update Order
      case 'update_order':
        $oID = tep_db_prepare_input($_GET['oID']);
        $status = tep_db_prepare_input($_POST['status']);

        // Set this Session's variables
        if (isset($_POST['billing_same_as_customer'])) $_SESSION['billing_same_as_customer'] = $_POST['billing_same_as_customer'];
        if (isset($_POST['shipping_same_as_billing'])) $_SESSION['shipping_same_as_billing'] = $_POST['shipping_same_as_billing'];
        
        // Set notifications variables
        if (sizeof($_POST) > 0) {
          $status = (isset($_POST['status']) ? tep_db_prepare_input($_POST['status']) : '');
          $comments = (isset($_POST['comments']) ? tep_db_prepare_input($_POST['comments']) : null);
          $notify = (isset($_POST['notify']) ? tep_db_prepare_input($_POST['notify']) : null);
          $notify_comments = (isset($_POST['notify_comments']) ? tep_db_prepare_input($_POST['notify_comments']) : null);
        }
        

          // Update Order Info
        //figure out the new currency value
        $currency_value_query = tep_db_query("SELECT value
                                              FROM " . TABLE_CURRENCIES . "
                                              WHERE code = '" . $_POST['update_info_payment_currency'] . "'");
        $currency_value = tep_db_fetch_array($currency_value_query);

      //figure out the country, state
        $update_customer_state = tep_get_zone_name($_POST['update_customer_country_id'], $_POST['update_customer_zone_id'], $_POST['update_customer_state']);
        $update_customer_country = tep_get_country_name($_POST['update_customer_country_id']);
        $update_billing_state = tep_get_zone_name($_POST['update_billing_country_id'], $_POST['update_billing_zone_id'], $_POST['update_billing_state']);
        $update_billing_country = tep_get_country_name($_POST['update_billing_country_id']);
        $update_delivery_state = tep_get_zone_name($_POST['update_delivery_country_id'], $_POST['update_delivery_zone_id'], $_POST['update_delivery_state']);
        $update_delivery_country = tep_get_country_name($_POST['update_delivery_country_id']);

        $sql_data_array = array(
          'customers_name' => tep_db_input(tep_db_prepare_input($_POST['update_customer_name'])),
          'customers_company' => tep_db_input(tep_db_prepare_input($_POST['update_customer_company'])),
          'customers_street_address' => tep_db_input(tep_db_prepare_input($_POST['update_customer_street_address'])),
          'customers_suburb' => tep_db_input(tep_db_prepare_input($_POST['update_customer_suburb'])),
          'customers_city' => tep_db_input(tep_db_prepare_input($_POST['update_customer_city'])),
          'customers_state' => tep_db_input(tep_db_prepare_input($update_customer_state)),
          'customers_postcode' => tep_db_input(tep_db_prepare_input($_POST['update_customer_postcode'])),
          'customers_country' => tep_db_input(tep_db_prepare_input($update_customer_country)),
          'customers_telephone' => tep_db_input(tep_db_prepare_input($_POST['update_customer_telephone'])),
          'customers_email_address' => tep_db_input(tep_db_prepare_input($_POST['update_customer_email_address'])),

          'billing_name' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_name'] : $_POST['update_billing_name']))),
          'billing_company' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_company'] : $_POST['update_billing_company']))),
          'billing_street_address' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_street_address'] : $_POST['update_billing_street_address']))),
          'billing_suburb' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_suburb'] : $_POST['update_billing_suburb']))),
          'billing_city' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_city'] : $_POST['update_billing_city']))),
          'billing_state' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $update_customer_state : $update_billing_state))),
          'billing_postcode' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_postcode'] : $_POST['update_billing_postcode']))),
          'billing_country' => tep_db_input(tep_db_prepare_input(((isset($_POST['billing_same_as_customer']) && $_POST['billing_same_as_customer'] == 'on') ? $update_customer_country : $update_billing_country))),

          'delivery_name' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_name'] : $_POST['update_billing_name']) : $_POST['update_delivery_name']))),
          'delivery_company' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_company'] : $_POST['update_billing_company']) : $_POST['update_delivery_company']))),
          'delivery_street_address' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_street_address'] : $_POST['update_billing_street_address']) : $_POST['update_delivery_street_address']))),
          'delivery_suburb' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_suburb'] : $_POST['update_billing_suburb']) : $_POST['update_delivery_suburb']))),
          'delivery_city' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_city'] : $_POST['update_billing_city']) : $_POST['update_delivery_city']))),
          'delivery_state' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $update_customer_state : $update_billing_state) : $update_delivery_state))),
          'delivery_postcode' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $_POST['update_customer_postcode'] : $_POST['update_billing_postcode']) : $_POST['update_delivery_postcode']))),
          'delivery_country' => tep_db_input(tep_db_prepare_input(((isset($_POST['shipping_same_as_billing']) && $_POST['shipping_same_as_billing'] == 'on') ? (($_POST['billing_same_as_customer'] == 'on') ? $update_customer_country : $update_billing_country) : $update_delivery_country))),

          'payment_method' => tep_db_input(tep_db_prepare_input($_POST['update_info_payment_method'])),
          'currency' => tep_db_input(tep_db_prepare_input($_POST['update_info_payment_currency'])),
          'currency_value' => tep_db_input(tep_db_prepare_input($currency_value['value'])),
          'cc_type' => tep_db_prepare_input($_POST['update_info_cc_type']),
          'cc_owner' => tep_db_prepare_input($_POST['update_info_cc_owner']),
          'cc_number' => tep_db_input(tep_db_prepare_input($_POST['update_info_cc_number'])),
          'cc_expires' => tep_db_prepare_input($_POST['update_info_cc_expires']),
          'last_modified' => 'now()');

        tep_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = \'' . tep_db_input($oID) . '\'');
        $order_updated = true;
        
        require ('order_editor/templates/update_status_history.php');
       
//////////////////
// Update Products
//////////////////
        if (is_array($_POST['update_products'])) {
          foreach($_POST['update_products'] as $orders_products_id => $products_details) {

          //  Update Inventory Quantity
          $order_query = tep_db_query("SELECT products_id, products_quantity
                                       FROM " . TABLE_ORDERS_PRODUCTS . "
                                       WHERE orders_id = '" . $oID . "'
                                       AND orders_products_id = '" . (int)$orders_products_id . "'");
          $order_products = tep_db_fetch_array($order_query);

          // First we do a stock check
          if ($products_details['qty'] != $order_products['products_quantity']){
            $quantity_difference = ($products_details['qty'] - $order_products['products_quantity']);
            if (STOCK_LIMITED == 'true'){
              tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET
                            products_quantity = products_quantity - " . $quantity_difference . ",
                            products_ordered = products_ordered + " . $quantity_difference . "
                            WHERE products_id = '" . (int)$order_products['products_id'] . "'");
  // QT Pro Addon BOF
              if (ORDER_EDITOR_USE_QTPRO == 'true') {
                $attrib_q = tep_db_query("select distinct op.products_id, po.products_options_id, pov.products_options_values_id
                                          from products_options po, products_options_values pov, products_options_values_to_products_options po2pov, orders_products_attributes opa, orders_products op
                                          where op.orders_id = '" . $oID . "'
                                          and op.orders_products_id = '" . $orders_products_id . "'
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
                tep_db_query("update ".TABLE_PRODUCTS_STOCK." set products_stock_quantity = products_stock_quantity - ".$quantity_difference . " where products_id= '" . $order_products['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
              }
  // QT Pro Addon EOF
            } else {
              tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
                             products_ordered = products_ordered + " . $quantity_difference . "
                             WHERE products_id = '" . (int)$order_products['products_id'] . "'");
            }
          }

          if ( (isset($products_details['delete'])) && ($products_details['delete'] == 'on') ) {
            //check first to see if product should be deleted
            //update quantities first
            if (STOCK_LIMITED == 'true'){
              tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET
                            products_quantity = products_quantity + " . $products_details["qty"] . ",
                            products_ordered = products_ordered - " . $products_details["qty"] . "
                            WHERE products_id = '" . (int)$order_products['products_id'] . "'");
// QT Pro Addon BOF
              if (ORDER_EDITOR_USE_QTPRO == 'true') {
                $attrib_q = tep_db_query("SELECT DISTINCT op.products_id, po.products_options_id, pov.products_options_values_id
                                          FROM products_options po, products_options_values pov, products_options_values_to_products_options po2pov, orders_products_attributes opa, orders_products op
                                          WHERE op.orders_id = '" . $oID . "'
                                          AND op.orders_products_id = '" . $orders_products_id . "'
                                          AND products_options_values_name = opa.products_options_values
                                          AND pov.products_options_values_id = po2pov.products_options_values_id
                                          AND po.products_options_id = po2pov.products_options_id
                                          AND products_options_name = opa.products_options");
                while($attrib_set = tep_db_fetch_array($attrib_q)) {
                  // corresponding to each option find the attribute ids ( opts and values id )

                  $products_stock_attributes[] = $attrib_set['products_options_id'].'-'.$attrib_set['products_options_values_id'];
                }
                sort($products_stock_attributes, SORT_NUMERIC); // Same sort as QT Pro stock
                $products_stock_attributes = implode($products_stock_attributes, ',');
                 // update the stock
                 tep_db_query("UPDATE " . TABLE_PRODUCTS_STOCK . " SET products_stock_quantity = products_stock_quantity + " . $products_details["qty"] . " WHERE products_id= '" . $order_products['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
              }
// QT Pro Addon EOF
            } else {
              tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
                             products_ordered = products_ordered - " . $products_details["qty"] . "
                             WHERE products_id = '" . (int)$order_products['products_id'] . "'");
            }

            tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . "
                          WHERE orders_id = '" . $oID . "'
                          AND orders_products_id = '" . (int)$orders_products_id . "'");

              tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                            WHERE orders_id = '" . $oID . "'
                            AND orders_products_id = '" . (int)$orders_products_id . "'");

              tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
                            WHERE orders_id = '" . $oID . "'
                            AND orders_products_id = '" . (int)$orders_products_id . "'");
          } else {
         //not deleted=> updated
            // Update orders_products Table
            $Query = "UPDATE " . TABLE_ORDERS_PRODUCTS . " SET
                      products_model = '" . $products_details["model"] . "',
                      products_name = '" . oe_html_quotes($products_details["name"]) . "',
                      products_price = '" . $products_details["price"] . "',
                      final_price = '" . $products_details["final_price"] . "',
                      products_tax = '" . $products_details["tax"] . "',
                      products_quantity = '" . $products_details["qty"] . "'
                      WHERE orders_id = '" . (int)$oID . "'
                      AND orders_products_id = '" . $orders_products_id . "';";
            tep_db_query($Query);

            // Update Any Attributes
            if(isset($products_details['attributes'])) {
              foreach($products_details['attributes'] as $orders_products_attributes_id => $attributes_details) {
                $Query = "UPDATE " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set
                          products_options = '" . $attributes_details["option"] . "',
                          products_options_values = '" . $attributes_details["value"] . "',
                          options_values_price ='" . $attributes_details["price"] . "',
                          price_prefix ='" . $attributes_details["prefix"] . "'
                          where orders_products_attributes_id = '$orders_products_attributes_id';";
                tep_db_query($Query);
              }//end of foreach($products_details["attributes"]
            }// end of if(isset($products_details[attributes]))
          } //end if/else product details delete= on
        } //end foreach post update products
      }//end if is-array update products

////////////////////////////
//update downloads if exists
////////////////////////////
      if (is_array($_POST['update_downloads'])) {
        foreach($_POST['update_downloads'] as $orders_products_download_id => $download_details) {
          $Query = "UPDATE " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " SET
                    orders_products_filename = '" . $download_details["filename"] . "',
                    download_maxdays = '" . $download_details["maxdays"] . "',
                    download_count = '" . $download_details["maxcount"] . "'
                    WHERE orders_id = '" . (int)$oID . "'
                    AND orders_products_download_id = '$orders_products_download_id';";
          tep_db_query($Query);
        }
      } //end downloads

///////////////////////////
//delete or update comments
///////////////////////////
      if (is_array($_POST['update_comments'])) {
        foreach($_POST['update_comments'] as $orders_status_history_id => $comments_details) {
          if (isset($comments_details['delete'])){
            $Query = "DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . "
                      WHERE orders_id = '" . (int)$oID . "'
                      AND orders_status_history_id = '$orders_status_history_id';";
            tep_db_query($Query);
          } else {
            $Query = "UPDATE " . TABLE_ORDERS_STATUS_HISTORY . " SET
                      comments = '" . $comments_details["comments"] . "'
                      WHERE orders_id = '" . (int)$oID . "'
                      AND orders_status_history_id = '$orders_status_history_id';";
            tep_db_query($Query);
          }
        }
      }//end comments update section
//////////////////////
// Set shipping module
//////////////////////

// reload_totals.php begins
// comparar con ese archivo
// Parece que incluye parches
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
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the shipping quotes- if we don't have shipping quotes shipping tax calculation can't happen
//////////////////////////////////////////////////////////////////////////////////////////////////
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
                //echo $order_totals[$i]['code'] . "<br>"; for debugging- use of this results in errors
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
            $new_order_totals[] = array('title' => $ot_title,
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


      if (isset($_POST['subaction'])) {
        switch($_POST['subaction']) {
          case 'add_product':
            tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=edit#products'));
            break;

        }
      }
      // 1.5 SUCCESS MESSAGE #####
      // CHECK FOR NEW EMAIL CONFIRMATION
      if ( (isset($_POST['nC1'])) || (isset($_POST['nC2'])) || (isset($_POST['nC3'])) ) {
        //then the user selected the option of sending a new email
        tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=email'));
        //redirect to the email case
      } else {
        //email? email?  We don't need no stinkin email!
        if ($order_updated)  {
        $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
        }
      tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=edit'));
      }
  break;
    ////
    // Edit Order
      case 'edit':
        if (!isset($_GET['oID'])) {
          $messageStack->add(ERROR_NO_ORDER_SELECTED, 'error');
          break;
          }
        $oID = tep_db_prepare_input($_GET['oID']);
        $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . $oID . "'");
        $order_exists = true;
        if (!tep_db_num_rows($orders_query)) {
        $order_exists = false;
          $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
          break;

        }

        $order = new manualOrder($oID);
        $shippingKey = $order->adjust_totals($oID);
        $order->adjust_zones();

        $cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();

// reload_totals.php end

        // Get the shipping quotes
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();


        break;
    }
  }

  // currecies drop-down array
  $currency_query = tep_db_query("select distinct title, code from " . TABLE_CURRENCIES . " order by code ASC");
  $currency_array = array();
  while($currency = tep_db_fetch_array($currency_query)) {
    $currency_array[] = array('id' => $currency['code'],
                              'text' => $currency['code'] . ' - ' . $currency['title']);
  }
  require(DIR_WS_INCLUDES . 'template_top.php');

  require('order_editor/css.php'); //because if you haven't got your css, what have you got?
?>

<script language="javascript" src="includes/general.js"></script>

<?php require('order_editor/javascript.php'); //because if you haven't got your javascript, what have you got? ?>

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
    <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
    </table>
    </td>
<!-- body_text //-->
    <td width="100%" valign="top">

 <?php
   if (($action == 'edit') && ($order_exists == true)) {

     echo tep_draw_form('edit_order', basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=update_order');
 ?>
    <div id="header">
      <p id="headerTitle" class="pageHeading"><?php echo sprintf(HEADING_TITLE, $oID, tep_datetime_short($order->info['date_purchased'])); ?></p>
        <ul>
          <li><?php echo tep_draw_button(IMAGE_ORDERS, "document", tep_href_link( FILENAME_ORDERS , 'oID=' . $oID . '&action=edit'), null); ?></li>
          <li><?php echo tep_draw_button(IMAGE_ORDERS_INVOICE, 'document', tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $_GET['oID']), null, array('newwindow' => true)); ?></li>
          <li><?php echo tep_draw_button(IMAGE_ORDERS_PACKINGSLIP, 'document', tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $_GET['oID']), null, array('newwindow' => true)) ?></li>
          <li><?php echo tep_draw_button(IMAGE_BACK, 'triangle-1-w', tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));?></li>
        </ul>
      </div>
      <div id="ordersMessageStack" style="float:left;width:100%;">
        <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
      </div>
      <div  style="columns: 400px;">
      <!-- customer_info bof //-->
            <!-- customer_info bof //-->
<!-- probar float:left -->
        <div style="min-height:200px;float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
      <?php require ("order_editor/templates/customer_info.php");?>
        </div>
              <!-- customer_info_eof //-->

              <!-- shipping_address bof -->
        <div style="min-height:200px;float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
      <?php require ("order_editor/templates/shipping.php");?>
        </div>
              <!-- shipping_address_eof //-->
                <!-- billing_address bof //-->
        <div style="min-height:200px;float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
        <?php require ("order_editor/templates/billing.php");?>
        </div>
                <!-- billing_address eof //-->

                <!-- payment_method bof //-->
        <div style="float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
        <?php require ("order_editor/templates/payment_method.php");?>
        </div>
                <!-- contact_information bof //-->
        <div style="float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
        <?php require ("order_editor/templates/contact_information.php");?>
        </div>
                <!-- contact_information eof //-->
        </div>
      </div>
    <div id="productsMessageStack" class="total" style="float:left;width:100%;">
      <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>
    <div width="100%" style="border: 1px solid #C9C9C9;">
      <a name="products"></a>
        <!-- product_listing bof //-->
        <table border="0" width="100%" cellspacing="0" cellpadding="2" id="productsTable">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent"><div align="center"><?php echo TABLE_HEADING_DELETE; ?></div></td>
            <td class="dataTableHeadingContent"><div align="center"><?php echo TABLE_HEADING_QUANTITY; ?></div></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_BASE_PRICE;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_BASE_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_PRICE_EXCL;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_UNIT_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_PRICE_INCL;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_UNIT_PRICE_TAXED;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_TOTAL_EXCL;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_TOTAL_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_TOTAL_INCL;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_TOTAL_PRICE_TAXED;?></td>
          </tr>
  <?php
  if (sizeof($order->products)) {
    for ($i=0; $i<sizeof($order->products); $i++) {
      $pID = $order->products[$i]['orders_products_id'];  ?>
          <tr class="dataTableRow">
            <td class="dataTableContent" valign="top"><div align="center"><input type="checkbox" name="<?php echo "update_products[" . $pID . "][delete]"; ?>" onClick="updateProductsField('delete', '<?php echo $pID; ?>', 'delete', this.checked, this)"></div></td>
            <td class="dataTableContent" valign="top"><div align=""><input type="tel" name="<?php echo "update_products[" . $pID . "][qty]"; ?>" size="2" onKeyUp="updatePrices('qty', '<?php echo $pID; ?>')" onChange="updateProductsField('reload1', '<?php echo $pID; ?>', 'products_quantity', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['qty']; ?>" id="<?php echo "update_products[" . $pID . "][qty]"; ?>"></div></td>
            <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $pID . "][name]"; ?>" size="50" onChange="updateProductsField('update', '<?php echo $pID; ?>', 'products_name', encodeURIComponent(this.value))" value='<?php echo oe_html_quotes($order->products[$i]['name']); ?>'>
    <?php
      // Has Attributes?
     if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
          $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
                echo '<br><nobr><small>&nbsp;<i> - ' . "<input name='update_products[$pID][attributes][$orders_products_attributes_id][option]' size='6' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['option']) . "' onChange=\"updateAttributesField('simple', 'products_options', '" . $orders_products_attributes_id . "', '" . $pID . "', encodeURIComponent(this.value))\">" . ': ' . "<input name='update_products[$pID][attributes][$orders_products_attributes_id][value]' size='10' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['value']) . "' onChange=\"updateAttributesField('simple', 'products_options_values', '" . $orders_products_attributes_id . "', '" . $pID . "', encodeURIComponent(this.value))\">" . ': ' . "</i><input name='update_products[$pID][attributes][$orders_products_attributes_id][prefix]' size='1' id='p" . $pID . "_" . $orders_products_attributes_id . "_prefix' value='" . $order->products[$i]['attributes'][$j]['prefix'] . "' onKeyUp=\"updatePrices('att_price', '" . $pID . "')\" onChange=\"updateAttributesField('hard', 'price_prefix', '" . $orders_products_attributes_id . "', '" . $pID . "', encodeURIComponent(this.value))\">" . ': ' . "<input name='update_products[$pID][attributes][$orders_products_attributes_id][price]' size='7' value='" . $order->products[$i]['attributes'][$j]['price'] . "' onKeyUp=\"updatePrices('att_price', '" . $pID . "')\" onChange=\"updateAttributesField('hard', 'options_values_price', '" . $orders_products_attributes_id . "', '" . $pID . "', encodeURIComponent(this.value))\" id='p". $pID . "a" . $orders_products_attributes_id . "'>";

                echo '</small></nobr>';
            }  //end for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {

             //Has downloads?

    if (DOWNLOAD_ENABLED == 'true') {
   $downloads_count = 1;
   $d_index = 0;
   $download_query_raw ="SELECT orders_products_download_id, orders_products_filename, download_maxdays, download_count
                         FROM orders_products_download
                         WHERE orders_products_id='" . $pID . "'
                         AND orders_id='" . $oID . "'
                         ORDER BY orders_products_download_id";

        $download_query = tep_db_query($download_query_raw);

        //
        if (isset($downloads->products)) unset($downloads->products);
        //

        if (tep_db_num_rows($download_query) > 0) {
        while ($download = tep_db_fetch_array($download_query)) {

        $downloads->products[$d_index] = array(
                    'id' => $download['orders_products_download_id'],
                    'filename' => $download['orders_products_filename'],
                    'maxdays' => $download['download_maxdays'],
                    'maxcount' => $download['download_count']);

        $d_index++;

        }
       }

   if (isset($downloads->products) && (sizeof($downloads->products) > 0)) {
    for ($mm=0; $mm<sizeof($downloads->products); $mm++) {
    $id =  $downloads->products[$mm]['id'];
    echo '<br><small>';
    echo '<nobr>' . ENTRY_DOWNLOAD_COUNT . $downloads_count . "";
    echo ' </nobr><br>' . "\n";


      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_FILENAME . ": <input name='update_downloads[" . $id . "][filename]' size='12' value='" . $downloads->products[$mm]['filename'] . "' onChange=\"updateDownloads('orders_products_filename', '" . $id . "', '" . $pID . "', this.value)\">";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXDAYS . ": <input name='update_downloads[" . $id . "][maxdays]' size='6' value='" . $downloads->products[$mm]['maxdays'] . "' onChange=\"updateDownloads('download_maxdays', '" . $id . "', '" . $pID . "', this.value)\">";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXCOUNT . ": <input name='update_downloads[" . $id . "][maxcount]' size='6' value='" . $downloads->products[$mm]['maxcount'] . "' onChange=\"updateDownloads('download_count', '" . $id . "', '" . $pID . "', this.value)\">";


     echo ' </nobr>' . "\n";
     echo '<br></small>';
     $downloads_count++;
     } //end  for ($mm=0; $mm<sizeof($download_query); $mm++) {
    }
   } //end download
  } //end if (sizeof($order->products[$i]['attributes']) > 0) {
?>
            </td>
            <td class="dataTableContent" valign="top"><input style="text-align:center;" name="<?php echo "update_products[" . $pID . "][model]"; ?>" size="12" onChange="updateProductsField('update', '<?php echo $pID; ?>', 'products_model', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['model']; ?>"></td>
            <td class="dataTableContent" valign="top"><input style="text-align:center;" name="<?php echo "update_products[" . $pID . "][tax]"; ?>" size="5" onKeyUp="updatePrices('tax', '<?php echo $pID; ?>')" onChange="updateProductsField('reload1', '<?php echo $pID; ?>', 'products_tax', encodeURIComponent(this.value))" value="<?php echo tep_display_tax_value($order->products[$i]['tax']); ?>" id="<?php echo "update_products[" . $pID . "][tax]"; ?>">%</td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][price]"; ?>" size="7" onKeyUp="updatePrices('price', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" value="<?php echo @number_format($order->products[$i]['price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $pID . "][price]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][final_price]"; ?>" size="7" onKeyUp="updatePrices('final_price', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" value="<?php echo @number_format($order->products[$i]['final_price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $pID . "][final_price]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][price_incl]"; ?>" size="7" value="<?php echo number_format(($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1)), 4, '.', ''); ?>" onKeyUp="updatePrices('price_incl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][price_incl]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][total_excl]"; ?>" size="8" value="<?php echo number_format($order->products[$i]['final_price'] * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_excl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][total_excl]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][total_incl]"; ?>" size="8" value="<?php echo number_format((($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1))) * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_incl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][total_incl]"; ?>"></td>
          </tr>

<?php
    }
  } else {
    //the order has no products
?>
          <tr class="dataTableRow">
            <td colspan="10" class="dataTableContent" valign="middle" align="center" style="padding: 20px 0 20px 0;"><?php echo TEXT_NO_ORDER_PRODUCTS; ?></td>
          </tr>
          <tr class="dataTableRow">
            <td colspan="10" style="border-bottom: 1px solid #C9C9C9;"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
          </tr>
<?php
  }
?>
        </table><!-- product_listing_eof //-->
        <div id="totalsBlock">
<?php require ("order_editor/templates/totalsBlock.php");?>
      </div>
    </div> <!-- this is end of the master div for the whole totals/shipping area -->

  <div>
<?php /*
    <a href="<?php echo tep_href_link('edit_orders_add_product.php', 'oID=' . $oID . '&step=1'); ?>" target="addProducts" onClick="openWindow('<?php echo tep_href_link('edit_orders_add_product.php', 'oID=' . $oID . '&step=1'); ?>','addProducts');return false"><?php echo tep_image_button('button_add_article.gif', TEXT_ADD_NEW_PRODUCT);
      ?></a>
*/
?>
    <?php echo tep_draw_button(TEXT_ADD_NEW_PRODUCT,'circle-plus',"javascript:openWindow('".tep_href_link('edit_orders_add_product.php', 'oID=' . $oID . '&step=1')."','addProducts');",'secondary'); ?><input type="hidden" name="subaction" value="">
    <input type="hidden" name="subaction" value="">
  </div>


    <div id="historyMessageStack">
        <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>
    <div id="commentsBlock">
<?php require ("order_editor/templates/commentsBlock.php");?>

  </div>


      <div>
      <?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?>
      </div>
      <br>

<table style="border: 1px solid #C9C9C9;" cellspacing="0" cellpadding="2" class="dataTableRow">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_NEW_STATUS; ?></td>
    <td class="main" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
  </tr>
    <tr>
      <td>
          <table border="0" cellspacing="0" cellpadding="2">

        <tr>
          <td class="main"><b><?php echo ENTRY_STATUS; ?></b></td>
          <td class="main" align="right"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status'], 'id="status"'); ?></td>
        </tr>
        <tr>
          <td class="main"><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b></td>
          <td class="main" align="right"><?php echo oe_draw_checkbox_field('notify', '', false, '', 'id="notify"'); ?></td>
        </tr>
        <tr>
          <td class="main"><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b></td>
          <td class="main" align="right"><?php echo oe_draw_checkbox_field('notify_comments', '', false, '', 'id="notify_comments"'); ?></td>
        </tr>
     </table>
      </td>
    <td class="main" width="10">&nbsp;</td>
    <td class="main">
    <?php echo tep_draw_textarea_field('comments', 'soft', '40', '5', '', 'id="comments"'); ?>
    </td>
  </tr>



<tr>
  <td colspan="3" align="center">
  <?php echo tep_draw_button (oe_html_no_quote(AJAX_SUBMIT_COMMENT),"plus", null,'secondary', array('params' => ' onClick="javascript:getNewComment();"'));?>
  </td>
</tr>
  </table>

    <div>
      <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>

    <!-- End of Status Block -->

<?php
  echo '</form>';
  }
?>
  <!-- body_text_eof //-->
      </td>
    </tr>
  </table>
  <!-- body_eof //-->
<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
  
  
  
    
?>
  <script>
  $(function() {
    $( document ).tooltip();
  });
  </script>
