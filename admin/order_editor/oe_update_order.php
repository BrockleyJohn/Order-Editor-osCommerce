<?php
//        $oID = tep_db_prepare_input($_GET['oID']);
        $status = tep_db_prepare_input($_POST['status']);

        // Set this Session's variables
        if (isset($_POST['billing_same_as_customer'])) $_SESSION['billing_same_as_customer'] = $_POST['billing_same_as_customer'];
        if (isset($_POST['shipping_same_as_billing'])) $_SESSION['shipping_same_as_billing'] = $_POST['shipping_same_as_billing'];

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

        
        // Agrega los campos definidos en $_POST en un array
        $order_data_array = array(
        'customers_name' => tep_db_input(tep_db_prepare_input($_POST['update_customer_name'])),
        'customers_nick' => tep_db_input(tep_db_prepare_input($_POST['update_customer_nick'])),
        'billing_nif' => tep_db_input(tep_db_prepare_input($_POST['update_customer_nif'])),
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
    // custom fields - delete
        'orders_peso' => tep_db_prepare_input($_POST['update_peso']),
        'orders_peso_vol' => tep_db_prepare_input($_POST['update_peso_vol']),
    // custom fields - delete
        'last_modified' => 'now()');

        tep_db_perform(TABLE_ORDERS, $order_data_array, 'update', 'orders_id = \'' . tep_db_input($oID) . '\'');
        $order_updated = true;


    // UPDATE STATUS HISTORY & SEND EMAIL TO CUSTOMER IF NECESSARY #####

        $check_status_query = tep_db_query("
                              SELECT customers_name, customers_email_address, orders_status, date_purchased
                              FROM " . TABLE_ORDERS . "
                              WHERE orders_id = '" . (int)$oID . "'");

// actualiza el status:
        $check_status = tep_db_fetch_array($check_status_query);

        if (($check_status['orders_status'] != $_POST['status']) || (tep_not_null($_POST['comments']))) {

          tep_db_query("UPDATE " . TABLE_ORDERS . " SET
                        orders_status = '" . tep_db_input($_POST['status']) . "',
                        last_modified = now()
                        WHERE orders_id = '" . (int)$oID . "'");

// envia un email:
// EMAIL BOF

          // Notify Customer ?
          $customer_notified = '0';
          if (isset($_POST['notify']) && ($_POST['notify'] == 'on')) {
          $notify_comments = '';
            if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) {
              $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $_POST['comments']) . "\n\n";
            }

            //Send text email
            $email = STORE_NAME . "\n" .
                     EMAIL_SEPARATOR . "\n" .
                     EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" .
                     EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int) $oID, 'SSL') . "\n" .
                     EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);

            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

            $customer_notified = '1';
          }

// EMAIL EOF
          tep_db_query("INSERT into " . TABLE_ORDERS_STATUS_HISTORY . "
          (orders_id, orders_status_id, date_added, customer_notified, comments)
          values ('" . tep_db_input($oID) . "',
              '" . tep_db_input($_POST['status']) . "',
              now(),
              " . tep_db_input($customer_notified) . ",
              '" . tep_db_input(tep_db_prepare_input($_POST['comments']))  . "')");
        }


        // Update Products
        if (is_array($_POST['update_products'])) {
          foreach($_POST['update_products'] as $orders_products_id => $products_details) {
            //  Update Inventory Quantity
            $order_query = tep_db_query("
            SELECT products_id, products_quantity
            FROM " . TABLE_ORDERS_PRODUCTS . "
            WHERE orders_id = '" . (int)$oID . "'
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
                  tep_db_query("update " . TABLE_PRODUCTS_STOCK . " set products_stock_quantity = products_stock_quantity - ".$quantity_difference . " where products_id= '" . $order_products['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
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
                     tep_db_query("update products_stock set products_stock_quantity = products_stock_quantity + ".$products_details["qty"] . " where products_id= '" . $order_products['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
                     }
// QT Pro Addon EOF
                } else {
                  tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
                                 products_ordered = products_ordered - " . $products_details["qty"] . "
                                 WHERE products_id = '" . (int)$order_products['products_id'] . "'");
                }

                    tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . "
                                  WHERE orders_id = '" . (int)$oID . "'
                                  AND orders_products_id = '" . (int)$orders_products_id . "'");

                    tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                                  WHERE orders_id = '" . (int)$oID . "'
                                  AND orders_products_id = '" . (int)$orders_products_id . "'");

                    tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
                                  WHERE orders_id = '" . (int)$oID . "'
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
                    AND orders_products_id = '$orders_products_id';";
                tep_db_query($Query);

              // Update Any Attributes
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


        //update any downloads that may exist
        if (is_array($_POST['update_downloads'])) {
        foreach($_POST['update_downloads'] as $orders_products_download_id => $download_details) {
          $Query = "UPDATE orders_products_download SET
                      orders_products_filename = '" . $download_details["filename"] . "',
                      download_maxdays = '" . $download_details["maxdays"] . "',
                      download_count = '" . $download_details["maxcount"] . "'
                      WHERE orders_id = '" . (int)$oID . "'
                      AND orders_products_download_id = '$orders_products_download_id';";
                      tep_db_query($Query);
              }
          }   //end downloads

        //delete or update comments
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

          $shipping = array();

        if (is_array($_POST['update_totals'])) {
          foreach($_POST['update_totals'] as $total_index => $total_details) {
            extract($total_details, EXTR_PREFIX_ALL, "ot");
            if ($ot_class == "ot_shipping") {

              $shipping['cost'] = $ot_value;
              $shipping['title'] = $ot_title;
              $shipping['id'] = $ot_id;

            } // end if ($ot_class == "ot_shipping")
            if ($ot_class == "ot_surcharge") {

                 $shipping['cost'] = $ot_value;
                 $shipping['title'] = $ot_title;
                 $shipping['id'] = $ot_id;

            } // end if ($ot_class == "ot_surcharge")
          } //end foreach
        } //end if is_array
/* */
      if (tep_not_null($shipping['id'])) {
        tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $shipping['id'] . "' WHERE orders_id = '" . (int)$oID . "'");
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


        //$tax=tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
// echo "TAXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=" . $GLOBALS[$module]->tax_class;
// OJITO: NO SE DE DONDE SE SACA $GLOBALS[$module]->tax_class
// ESTABA FIJADO A 3 en este archivo por alguien...
// lo cambio por 
//        $tax = tep_get_tax_rate(3, $order->delivery['country']['id'], $order->delivery['zone_id']);
        $tax = tep_get_tax_rate($shipping_modules, $order->delivery['country']['id'], $order->delivery['zone_id']);
        $order->info['total'] -= ( $order->info['shipping_cost'] - ($order->info['shipping_cost'] / (1 + ($tax /100))) );

        if (DISPLAY_PRICE_WITH_TAX == 'true') {//extract the base shipping cost or the ot_shipping module will add tax to it again
          $module = substr($GLOBALS['shipping']['id'], 0, strpos($GLOBALS['shipping']['id'], '_'));
          $order->info['shipping_cost'] = ($order->info['shipping_cost'] / (1 + ($tax /100)));
        }

        //this is where we call the order total modules
        require( 'order_editor/order_total.php');
        $order_total_modules = new order_total();
        $order_total_modules->prepare_order_total_values(); // RusNN all totals work
        $order_totals = $order_total_modules->process();

        $current_ot_totals_array = array();
        $current_ot_titles_array = array();
        $current_ot_totals_query = tep_db_query("select class, title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "' order by sort_order");
        while ($current_ot_totals = tep_db_fetch_array($current_ot_totals_query)) {
          $current_ot_totals_array[] = $current_ot_totals['class'];
          $current_ot_titles_array[] = $current_ot_totals['title'];
        }

        tep_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . (int)$oID . "'");

        $j=1; //giving something a sort order of 0 ain't my bag baby
        $new_order_totals = array();
/// added fix
        $tax_to_add=0;
/// added fix
        if (is_array($_POST['update_totals'])) { //1
          foreach($_POST['update_totals'] as $total_index => $total_details) { //2
            extract($total_details, EXTR_PREFIX_ALL, "ot");
// cambiado por JM http://comercianos.com/alguien-usa-order-editor-t734.html
//            if (!strstr($ot_class, 'ot_custom')) { //3
//
            if ((strstr($ot_class, 'ot_subtotal')) || (strstr($ot_class, 'ot_total'))) { //3
        /*
// RusNN order totals correction for right work BOF
                        // ???? ????? ??????? ?????? ??? ? ?????? $order_totals, ?? ??? ?? ??????? ???????????? ? $_POST['update_totals'],
                        // ????? ?? ???? ?????? ? ?????????? ????????, ????? ?????? ?? ????? ????????, ????? ??????????? ?????, ? ? ??????
                        // ??????????? ??????? (? ? ??????? ?????? ?? ??????) ????? ?????, ?? ?????? ?????
                         $found = false;
                         for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
                           if ($order_totals[$i]['code'] == $ot_class) $found = true;
                         }
                         if (!$found) continue;
// RusNN order totals correction for right work EOF
*/
             for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //4

        if ($order_totals[$i]['code'] == 'ot_tax') { //5
        $new_ot_total = ((in_array($order_totals[$i]['title'], $current_ot_titles_array)) ? false : true);
        } else { //within 5
        $new_ot_total = ((in_array($order_totals[$i]['code'], $current_ot_totals_array)) ? false : true);
        }  //end 5 if ($order_totals[$i]['code'] == 'ot_tax')

        if ( ( ($order_totals[$i]['code'] == 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) && ($order_totals[$i]['title'] == $ot_title) ) || ( ($order_totals[$i]['code'] != 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) ) ) { //6
        //only good for components that show up in the $order_totals array
/// added fix
          if ($order_totals[$i]['code'] == 'ot_tax') {
            $order_totals[$i]['value']+=$tax_to_add;
            $order_totals[$i]['text']=$currencies->format($order_totals[$i]['value'], true, $order->info['currency'], $order->info['currency_value']);
          }
/// added fix
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
                echo $order_totals[$i]['code'] . "<br>";  ////for debugging- use of this results in errors

              } elseif ($new_ot_total) { //also within 6
                $order->info['total'] += ($order_totals[$i]['value']*(-1));
                $current_ot_totals_array[] = $order_totals[$i]['code'];
                $written_ot_totals_array[] = $ot_class;
                $written_ot_titles_array[] = $ot_title;
              }//end 6
           }//end 4
         } elseif ( (tep_not_null($ot_value)) && (tep_not_null($ot_title)) ) { // this modifies if (!strstr($ot_class, 'ot_custom')) { //3
/// added fix
            //This calculates tax on ot_custom
            $tax_to_add += $ot_value * $tax / (100 + $tax);
/// added fix
            $new_order_totals[] = array('title' => $ot_title,
                     'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                                        'value' => $ot_value,
// cambiado por jm http://comercianos.com/alguien-usa-order-editor-t734.html
//                                        'code' => 'ot_custom_' . $j,
                                        'code' => ((strstr($ot_class, 'ot_custom')) ? 'ot_custom_' . $j : $ot_class),
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
/// added fix
                //This calculates tax on non-standard
                $tax_to_add += $ot_value * $tax / (100 + $tax);
                $order->info['total'] += $ot_value;
/// added fix
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
                                  'title' => $new_order_totals[$i]['title'],
                                  'text' => $new_order_totals[$i]['text'],
                                  'value' => $new_order_totals[$i]['value'],
                                  'class' => $new_order_totals[$i]['code'],
                                  'sort_order' => $new_order_totals[$i]['sort_order']);
          tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
        }


        if (isset($_POST['subaction'])) {
          switch($_POST['subaction']) {
            case 'add_product':
              // $messageStack->add_session('ho ho ho!', 'success');
              tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=edit#products'));
              break;

          }
        }

        // 1.5 SUCCESS MESSAGE #####


    // CHECK FOR NEW EMAIL CONFIRMATION

    /// nC1????? nC2????? nC3????? Localizar si existen
    if ( (isset($_POST['nC1'])) || (isset($_POST['nC2'])) || (isset($_POST['nC3'])) ) {
    //then the user selected the option of sending a new email

    tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=email'));
    //redirect to the email case

  } else  {
     //email? email?  We don't need no stinkin email!

   if ($order_updated)  {
      $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
    }

    tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=edit'));

    }
