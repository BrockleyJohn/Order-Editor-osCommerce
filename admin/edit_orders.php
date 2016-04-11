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
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
/*
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
*/
$orders_statuses = array();
$orders_statuses = tep_get_orders_status();

$action = (isset($_GET['action']) ? $_GET['action'] : 'edit');

  if (isset($action)) {
    switch ($action) {
    ////
    // Update Order
      case 'update_order':
        $oID = tep_db_prepare_input($_GET['oID']);
        $status = tep_db_prepare_input($_POST['status']);

        // Set this Session's variables
        // check if it's useful or not
        // most probably not
        if (isset($_POST['billing_same_as_customer'])) $_SESSION['billing_same_as_customer'] = $_POST['billing_same_as_customer'];
        if (isset($_POST['shipping_same_as_billing'])) $_SESSION['shipping_same_as_billing'] = $_POST['shipping_same_as_billing'];
        
        // Set notifications variables
        if (sizeof($_POST) > 0) {
          $status = (isset($_POST['status']) ? tep_db_prepare_input($_POST['status']) : '');
          $comments = (isset($_POST['comments']) ? tep_db_prepare_input($_POST['comments']) : null);
          $notify = (isset($_POST['notify']) ? tep_db_prepare_input($_POST['notify']) : null);
          $notify_comments = (isset($_POST['notify_comments']) ? tep_db_prepare_input($_POST['notify_comments']) : null);
        }
        

          // Update Order Info (customer, billing, delivery and payment method)
        //figure out the new currency value
        $currency_value_query = tep_db_query("SELECT value
                                              FROM " . TABLE_CURRENCIES . "
                                              WHERE code = '" . $_POST['update_info_payment_currency'] . "'");
        $currency_value = tep_db_fetch_array($currency_value_query);
      

        require ('order_editor/actions/update_orders_table.php');
        require ('order_editor/actions/update_status_history.php');
       
//////////////////
// Update Products
//////////////////
        if (is_array($_POST['update_products'])) {


/// OJO AQUI DEFINE $products_details que no existe en ajax
/// en ajax usa  $order_products
          foreach($_POST['update_products'] as $pID => $products_details) {
            $quantity = $products_details['qty'];
            require ('order_editor/2.php');

            if ( (isset($products_details['delete'])) && ($products_details['delete'] == 'on') ) {
              require ('order_editor/7.php');

            } else { //not deleted=> updated
              // Update orders_products Table
              $Query = "UPDATE " . TABLE_ORDERS_PRODUCTS . " SET
                        products_model = '" . $products_details["model"] . "',
                        products_name = '" . oe_html_quotes($products_details["name"]) . "',
                        products_price = '" . $products_details["price"] . "',
                        final_price = '" . $products_details["final_price"] . "',
                        products_tax = '" . $products_details["tax"] . "',
                        products_quantity = '" . $products_details["qty"] . "'
                        WHERE orders_id = '" . (int)$oID . "'
                        AND orders_products_id = '" . $pID . "';";
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

require ("order_editor/actions/reload_totals.php");


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
<script>
$(function() {
  $( document ).tooltip();
});
</script>
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" border="0" valign="top">

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
    <div id="ordersMessageStack" style="float:left;width:100%;"><br /></div>
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
    <div id="productsMessageStack" class="total" style="float:left;width:100%;"><br /></div>
    <div>
      <a name="products"></a>
        <!-- product_listing bof //-->
      <div id="product_listingBlock">
        <?php require ("order_editor/templates/product_listing.php");?>
      </div>
        <!-- product_listing_eof //-->
      <div id="totalsBlock">
        <?php require ("order_editor/templates/totals.php");?>
      </div>
    </div> <!-- this is end of the master div for the whole totals/shipping area -->

    <div>
      <?php echo tep_draw_button(TEXT_ADD_NEW_PRODUCT,'circle-plus',"javascript:openWindow('".tep_href_link('edit_orders_add_product.php', 'oID=' . $oID . '&step=1')."','addProducts');",'secondary'); ?><input type="hidden" name="subaction" value="">
      <input type="hidden" name="subaction" value="">
    </div>


    <div id="historyMessageStack"><br /></div>
    <div id="commentsBlock">
      <?php require ("order_editor/templates/commentsBlock.php");?>
    </div>


    <div>
      <?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?>
    </div>
    <br>
    <div id="updateStatusBlock">
      <?php require ("order_editor/templates/updateStatusBlock.php");?>
    </div>
    <div>
      <br />
    </div>
    <div id="fade"></div>
        <div id="modal">
            <img id="loader" src="order_editor/images/loading.gif" />
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

