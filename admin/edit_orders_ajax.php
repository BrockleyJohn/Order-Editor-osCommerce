<?php
  /*
  $Id: edit_orders_ajax.php v5.0.7 11/18/2009 surfalot Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  Released under the GNU General Public License

  For Order Editor support or to post bug reports, feature requests, etc, please visit the Order Editor support thread:
  http://forums.oscommerce.com/index.php?showtopic=54032

  Eliminados addons superfluos 31/03/2016 JMC

*/

  require('includes/application_top.php');

  // output a response header
  header('Content-type: text/html; charset=' . CHARSET . '');

  // require the appropriate functions & classes
  require('order_editor/functions.php');
  require('order_editor/manualcart.php');
  require('order_editor/manualorder.php');
  require('order_editor/shipping.php');
  require(DIR_WS_LANGUAGES . $language. '/' . 'edit_orders.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  //$action
  //all variables are sent by $_GET only or by $_POST only, never together
  if (sizeof($_GET) > 0) {
    $action = $_GET['action'];
    $oID = (isset($_GET['oID']) ? tep_db_prepare_input($_GET['oID']) : '');
    $pID = (isset($_GET['pID']) ? tep_db_prepare_input($_GET['pID']) : '');
    $status = (isset($_GET['status']) ? tep_db_prepare_input($_GET['status']) : '');
    $comments = (isset($_GET['comments']) ? tep_db_prepare_input($_GET['comments']) : null);
    $notify = (isset($_GET['notify']) ? tep_db_prepare_input($_GET['notify']) : null);
    $notify_comments = (isset($_GET['notify_comments']) ? tep_db_prepare_input($_GET['notify_comments']) : null);
    
  } elseif (sizeof($_POST) > 0) {

    $action = $_POST['action'];
    $oID = (isset($_POST['oID']) ? tep_db_prepare_input($_POST['oID']) : '');
   }

  //1.  Update most the orders table
  if ($action == 'update_order_field') {
   tep_db_query("UPDATE " . TABLE_ORDERS . 
                " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . 
                "' WHERE orders_id = '" . $oID . "'");
    //generate responseText
    echo $_GET['field'];
  }

  //2.  Update the orders_products table for qty, tax, name, or model
  if ($action == 'update_product_field') {
    if ($_GET['field'] == 'products_quantity') {
      $quantity = $_GET['new_value'];
      require ('order_editor/2.php');
    }//end if ($_GET['field'] = 'products_quantity'
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . 
                 " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . 
                 "' WHERE orders_products_id = '" . $pID . "' AND orders_id = '" . $oID . "'");
    //generate responseText
    echo $_GET['field'];

  }
  //3.  Update the orders_products table for price and final_price (interdependent values)
  if ($action == 'update_product_value_field') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . 
                 " SET products_price = '" . tep_db_input(tep_db_prepare_input($_GET['price'])) . "', 
                 final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . 
                 "' WHERE orders_products_id = '" . $pID . "' AND orders_id = '" . $oID . "'");

    //generate responseText
    echo TABLE_ORDERS_PRODUCTS;

  }


    //5.  Update the orders_products_download table
  if ($action == 'update_downloads') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . 
                 " SET " . $_GET['field'] . " = '" . tep_db_input(tep_db_prepare_input($_GET['new_value'])) . 
                 "' WHERE orders_products_download_id = '" . $_GET['did'] . 
                 "' AND orders_products_id = '" . $pID . "' AND orders_id = '" . $oID . "'");

   //generate responseText
    echo $_GET['field'];

  }

  //6. Update the currency of the order
  if ($action == 'update_currency') {
      tep_db_query("UPDATE " . TABLE_ORDERS . 
                   " SET currency = '" . tep_db_input(tep_db_prepare_input($_GET['currency'])) . 
                   "', currency_value = '" . tep_db_input(tep_db_prepare_input($_GET['currency_value'])) . 
                   "' WHERE orders_id = '" . $oID . "'");

     //generate responseText
    echo $_GET['currency'];

  }//end if ($action == 'update_currency') {


  //7.  Update most any field in the orders_products table
  if ($action == 'delete_product_field') {

          //  Update Inventory Quantity
          $order_query = tep_db_query("SELECT products_id, products_quantity
                                       FROM " . TABLE_ORDERS_PRODUCTS . "
                                       WHERE orders_id = '" . $oID . "'
                                       AND orders_products_id = '" . $pID . "'");
          $order_products = tep_db_fetch_array($order_query);
          $quantity = $order_products['products_quantity'];
	require ('order_editor/7.php');

      //generate responseText
    echo TABLE_ORDERS_PRODUCTS;

  }

    //4.  Update the orders_products_attributes table
  if ($action == 'update_attributes_field') {
    tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . 
                 " SET " . $_GET['field'] . " = '" . oe_iconv($_GET['new_value']) . 
                 "' WHERE orders_products_attributes_id = '" . $_GET['aid'] . 
                 "' AND orders_products_id = '" . $pID . 
                 "' AND orders_id = '" . $oID . "'");

    if (isset($_GET['final_price'])) {
      tep_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS . 
                   " SET final_price = '" . tep_db_input(tep_db_prepare_input($_GET['final_price'])) . 
                   "' WHERE orders_products_id = '" . $pID . 
                   "' AND orders_id = '" . $oID . "'");
    }
    //generate responseText
    echo $_GET['field'];
  }

  //8. Update the orders_status_history table
  if ($action == 'delete_comment') {
    tep_db_query("DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . 
                 " WHERE orders_status_history_id = '" . $_GET['cID'] . 
                 "' AND orders_id = '" . $oID . "'");
    //generate responseText
    echo TABLE_ORDERS_STATUS_HISTORY;
  }

  //9. Update the orders_status_history table
  if ($action == 'update_comment') {
    tep_db_query("UPDATE " . TABLE_ORDERS_STATUS_HISTORY . 
                 " SET comments = '" . oe_iconv($_GET['comment']) . 
                 "' WHERE orders_status_history_id = '" . $_GET['cID'] . 
                 "' AND orders_id = '" . $oID . "'");
    //generate responseText
    echo TABLE_ORDERS_STATUS_HISTORY;
  }


  //10. Reload the shipping and order totals block
    if ($action == 'reload_totals') {

require ("order_editor/actions/reload_totals.php");

        $order = new manualOrder($oID);
        $shippingKey = $order->adjust_totals($oID);
        $order->adjust_zones();

        $cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();

// reload_totals.php end

      
require ('order_editor/templates/totals.php');



    }//end if ($action == 'reload_totals') {


  //11. insert new comments
   if ($action == 'insert_new_comment') {
    //orders status
    $orders_statuses = array();
    $orders_statuses = tep_get_orders_status();

     require ('order_editor/actions/update_status_history.php');
     require ('order_editor/templates/commentsBlock.php');
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

    tep_db_query("UPDATE " . TABLE_ORDERS . 
                 " SET shipping_module = '" . $_GET['id'] . 
                 "' WHERE orders_id = '" . $oID . "'");

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

    require ('order_editor/templates/totals.php');
     
     
  } //end if ($action == 'insert_shipping') {


  //13. new order email

  if ($action == 'new_order_email')  {

    require ('order_editor/actions/new_order_email.php');

  } //end if ($action == 'new_order_email') {
