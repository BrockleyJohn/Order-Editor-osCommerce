<?php
  /*
  $Id: edit_orders_ajax.php v5.0.5 08/27/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

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
//  include('order_editor/http_client.php');
  include(DIR_WS_LANGUAGES . $language. '/' . 'edit_orders.php');


  // Include currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  
  //$action
  //all variables are sent by $_GET only or by $_POST only, never together
  if (sizeof($_GET) > 0) {

    $action = $_GET['action'];
    $oID = (isset($_GET['oID']) ? tep_db_prepare_input($_GET['oID']) : '');
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
      AND orders_products_id = '" . $_GET['pid'] . "'");
      $orders_product_info = tep_db_fetch_array($order_query);

      // stock check

      if ($_GET['new_value'] != $orders_product_info['products_quantity']){
      $quantity_difference = ($_GET['new_value'] - $orders_product_info['products_quantity']);
        if (STOCK_LIMITED == 'true'){
            tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET
          products_quantity = products_quantity - " . $quantity_difference . ",
          products_ordered = products_ordered + " . $quantity_difference . "
          WHERE products_id = '" . $orders_product_info['products_id'] . "'");
          } else {
          tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
          products_ordered = products_ordered + " . $quantity_difference . "
          WHERE products_id = '" . $orders_product_info['products_id'] . "'");
        } //end if (STOCK_LIMITED == 'true'){
      } //end if ($_GET['new_value'] != $orders_product_info['products_quantity']){
    }//end if ($_GET['field'] = 'products_quantity'

    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $oID . "'");



    //generate responseText
    echo $_GET['field'];

  }

  //3.  Update the orders_products table for price and final_price (interdependent values)
  if ($action == 'update_product_value_field') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET products_price = '" . tep_db_input(tep_db_prepare_input($_GET['price'])) . "', final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $oID . "'");

    //generate responseText
    echo TABLE_ORDERS_PRODUCTS;

  }

    //4.  Update the orders_products_attributes table
  if ($action == 'update_attributes_field') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . "' WHERE orders_products_attributes_id = '" . $_GET['aid'] . "' AND orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $oID . "'");

    if (isset($_GET['final_price'])) {
      tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . "' WHERE orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $oID . "'");
    }
    //generate responseText
    echo $_GET['field'];
  }

    //5.  Update the orders_products_download table
if ($action == 'update_downloads') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " SET " . $_GET['field'] . " = '" . tep_db_input(tep_db_prepare_input($_GET['new_value'])) . "' WHERE orders_products_download_id = '" . $_GET['did'] . "' AND orders_products_id = '" . $_GET['pid'] . "' AND orders_id = '" . $oID . "'");

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
            $order_query = tep_db_query("
            SELECT products_id, products_quantity
            FROM " . TABLE_ORDERS_PRODUCTS . "
            WHERE orders_id = '" . $oID . "'
            AND orders_products_id = '" . $_GET['pid'] . "'");
            $order = tep_db_fetch_array($order_query);

             //update quantities first
              if (STOCK_LIMITED == 'true'){
                tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET
                              products_quantity = products_quantity + " . $order['products_quantity'] . ",
                              products_ordered = products_ordered - " . $order['products_quantity'] . "
                              WHERE products_id = '" . (int)$order['products_id'] . "'");
              } else {
                     tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
                                    products_ordered = products_ordered - " . $order['products_quantity'] . "
                                    WHERE products_id = '" . (int)$order['products_id'] . "'");
                                    }

                    tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . "
                                  WHERE orders_id = '" . $oID . "'
                                  AND orders_products_id = '" . $_GET['pid'] . "'");

                    tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                                  WHERE orders_id = '" . $oID . "'
                                  AND orders_products_id = '" . $_GET['pid'] . "'");

                    tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
                                  WHERE orders_id = '" . $oID . "'
                                  AND orders_products_id = '" . $_GET['pid'] . "'");

      //generate responseText
    echo TABLE_ORDERS_PRODUCTS;

  }

        //delete or update comments
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


include ("order_editor/ajax/reload_totals.php");
include ("order_editor/templates/totalsBlock.php");


  }//end if ($action == 'reload_shipping') {


  //11. insert new comments



   if ($action == 'insert_new_comment') {

include ("order_editor/ajax/insert_new_comment.php");

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
    
      include ("order_editor/ajax/insert_shipping.php");

  } //end if ($action == 'insert_shipping') {

  //13. new order email

    if ($action == 'new_order_email')  {
include ("order_editor/ajax/new_order_email.php");

 } //end if ($action == 'new_order_email')  {  ?>