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

  $oID = (isset($_GET['oID']) ? tep_db_prepare_input($_GET['oID']) : '');
  
        if (!$oID) {
        $messageStack->add(ERROR_NO_ORDER_SELECTED, 'error');
          break;
          }
//        $oID = tep_db_prepare_input($_GET['oID']);
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

        // Get the shipping quotes
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();

  // currecies drop-down array
  $currency_query = tep_db_query("select distinct title, code from " . TABLE_CURRENCIES . " order by code ASC");
  $currency_array = array();
  while($currency = tep_db_fetch_array($currency_query)) {
    $currency_array[] = array('id' => $currency['code'],
                              'text' => $currency['code'] . ' - ' . $currency['title']);
  }
  require(DIR_WS_INCLUDES . 'template_top.php');
?>
 
<?php include('order_editor/css.php'); //because if you haven't got your css, what have you got? ?>

<script language="javascript" src="includes/general.js"></script>

<?php include('order_editor/javascript.php'); //because if you haven't got your javascript, what have you got? ?>


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
      <div id="ordersMessageStack">
        <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
      </div>
    <!-- customer_info bof //-->
      <table border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td valign="top">
          <!-- customer_info bof //-->
          <table width="100%" border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
            <tr class="dataTableHeadingRow">
              <td colspan="4" class="dataTableHeadingContent" valign="top"><?php echo ENTRY_CUSTOMER; ?></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_NAME; ?></td>
              <td colspan="3" valign="top" class="dataTableContent"><input name="update_customer_name" size="37" value="<?php echo stripslashes($order->customer['name']); ?>" onChange="updateOrdersField('customers_name', encodeURIComponent(this.value))"></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_COMPANY; ?></td>
              <td colspan="3" valign="top" class="dataTableContent"><input name="update_customer_company" size="37" value="<?php echo stripslashes($order->customer['company']); ?>" onChange="updateOrdersField('customers_company', encodeURIComponent(this.value))"></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_STREET_ADDRESS; ?></td>
              <td colspan="3" valign="top" class="dataTableContent" nowrap><input name="update_customer_street_address" size="37" value="<?php echo stripslashes($order->customer['street_address']); ?>" onChange="updateOrdersField('customers_street_address', encodeURIComponent(this.value))"></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_SUBURB; ?></td>
              <td colspan="3" valign="top" class="dataTableContent" nowrap><input name="update_customer_suburb" size="37" value="<?php echo stripslashes($order->customer['suburb']); ?>" onChange="updateOrdersField('customers_suburb', encodeURIComponent(this.value))"></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_CITY_STATE; ?></td>
              <td colspan="2" valign="top" class="dataTableContent" nowrap><input name="update_customer_city" size="15" value="<?php echo stripslashes($order->customer['city']); ?>" onChange="updateOrdersField('customers_city', encodeURIComponent(this.value))">,</td>
              <td valign="top" class="dataTableContent"><span id="customerStateMenu">
                <?php echo tep_draw_pull_down_menu('update_customer_zone_id', tep_get_country_zones($order->customer['country_id']), $order->customer['zone_id'], 'style="width: 141px;" onChange="updateOrdersField(\'customers_state\', this.options[this.selectedIndex].text);"');?>
                </span><span id="customerStateInput"><input name="update_customer_state" size="15" value="<?php echo stripslashes($order->customer['state']); ?>" onChange="updateOrdersField('customers_state', encodeURIComponent(this.value))"></span></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_POST_CODE; ?></td>
              <td class="dataTableContent" valign="top"><input name="update_customer_postcode" size="5" value="<?php echo $order->customer['postcode']; ?>" onChange="updateOrdersField('customers_postcode', encodeURIComponent(this.value))"></td>
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_COUNTRY; ?></td>
              <td class="dataTableContent" valign="top"><?php echo '                ' . tep_draw_pull_down_menu('update_customer_country_id', tep_get_countries(), $order->customer['country_id'], 'style="width: 141px;" onChange="update_zone(\'update_customer_country_id\', \'update_customer_zone_id\', \'customerStateInput\', \'customerStateMenu\'); updateOrdersField(\'customers_country\', this.options[this.selectedIndex].text);"');?></td>
            </tr>
            <tr class="dataTableRow">
              <td colspan="4" style="border-top: 1px solid #C9C9C9;"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
              <td colspan="3" valign="top" class="dataTableContent"><input type="tel" name="update_customer_telephone" size="15" value="<?php echo $order->customer['telephone']; ?>" onChange="updateOrdersField('customers_telephone', encodeURIComponent(this.value))"></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
              <td colspan="3" valign="top" class="dataTableContent"><input name="update_customer_email_address" size="35" value="<?php echo $order->customer['email_address']; ?>" onChange="updateOrdersField('customers_email_address', encodeURIComponent(this.value))"></td>
            </tr>
          </table>
            <!-- customer_info_eof //-->
            <!-- shipping_address bof -->
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #C9C9C9;">
            <tr>
              <td class="dataTableContent">
              <table width="100%" cellspacing="0" cellpadding="2">
                <tr class="dataTableHeadingRow">
                 <td class="dataTableHeadingContent" valign="top" title="<?php echo HINT_SHIPPING_ADDRESS;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo ENTRY_SHIPPING_ADDRESS; ?></td>
            </tr>
          </table>
         </td>
        </tr>
              <tr id="shippingAddressEntry">
                <td class="dataTableContent">
                <table width="100%" cellspacing="0" cellpadding="2">
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_NAME; ?></td>
                    <td colspan="3" valign="top" class="dataTableContent"><input name="update_delivery_name" size="37" value="<?php echo stripslashes($order->delivery['name']); ?>" onChange="updateOrdersField('delivery_name', encodeURIComponent(this.value))"></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_COMPANY; ?></td>
                    <td colspan="3" valign="top" class="dataTableContent"><input name="update_delivery_company" size="37" value="<?php echo stripslashes($order->delivery['company']); ?>" onChange="updateOrdersField('delivery_company', encodeURIComponent(this.value))"></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_STREET_ADDRESS; ?></td>
                    <td colspan="3" valign="top" class="dataTableContent"><input name="update_delivery_street_address" size="37" value="<?php echo stripslashes($order->delivery['street_address']); ?>" onChange="updateOrdersField('delivery_street_address', encodeURIComponent(this.value))"></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_SUBURB; ?></td>
                    <td colspan="3" valign="top" class="dataTableContent"><input name="update_delivery_suburb" size="37" value="<?php echo stripslashes($order->delivery['suburb']); ?>" onChange="updateOrdersField('delivery_suburb', encodeURIComponent(this.value))"></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_CITY_STATE; ?></td>
                    <td colspan="2" valign="top" class="dataTableContent" nowrap><input name="update_delivery_city" size="15" value="<?php echo stripslashes($order->delivery['city']); ?>" onChange="updateOrdersField('delivery_city', encodeURIComponent(this.value))">,</td>
                    <td valign="top" class="dataTableContent"><span id="deliveryStateMenu">

                    <?php echo tep_draw_pull_down_menu('update_delivery_zone_id', tep_get_country_zones($order->delivery['country_id']), $order->delivery['zone_id'], 'style="width: 141px;" onChange="updateShippingZone(\'delivery_state\', this.options[this.selectedIndex].text);"'); ?>
                    </span><span id="deliveryStateInput"><input name="update_delivery_state" size="15" value="<?php echo stripslashes($order->delivery['state']); ?>" onChange="updateShippingZone('delivery_state', encodeURIComponent(this.value))"></span></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_POST_CODE; ?></td>
                    <td class="dataTableContent" valign="top"><input name="update_delivery_postcode" size="5" value="<?php echo $order->delivery['postcode']; ?>" onChange="updateShippingZone('delivery_postcode', encodeURIComponent(this.value))"></td>
                    <td class="dataTableContent" valign="middle" align="right"><?php echo ENTRY_COUNTRY; ?></td>
                    <td class="dataTableContent" valign="top">
                    <?php echo tep_draw_pull_down_menu('update_delivery_country_id', tep_get_countries(), $order->delivery['country_id'], 'style="width: 141px;" onchange="update_zone(\'update_delivery_country_id\', \'update_delivery_zone_id\', \'deliveryStateInput\', \'deliveryStateMenu\'); updateShippingZone(\'delivery_country\', this.options[this.selectedIndex].text);"'); ?>
                    </td>
                  </tr>
                </table>
                </td>
              </tr>
            </table>
            <!-- shipping_address_eof //-->
            </td>
            <td valign="top" width="10">&nbsp;</td>
            <td valign="top">
            <table width="300" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #C9C9C9;">
              <!-- billing_address bof //-->
              <tr>
                <td class="dataTableContent">
                <table width="100%" cellspacing="0" cellpadding="2">
                  <tr class="dataTableHeadingRow">
                    <td colspan="4" class="dataTableHeadingContent" valign="top"><?php echo ENTRY_BILLING_ADDRESS; ?></td>
                  </tr>
                </table>
                </td>
              </tr>
              <tr id="billingAddressEntry">
                <td class="dataTableContent">
                <table width="100%" cellspacing="0" cellpadding="2">
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_NAME; ?></td>
                    <td colspan="3" valign="top" class="dataTableContent"><input name="update_billing_name" size="37" value="<?php echo stripslashes($order->billing['name']); ?>" onChange="updateOrdersField('billing_name', encodeURIComponent(this.value))"></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_COMPANY; ?></td>
                    <td colspan="3" valign="top" class="dataTableContent"><input name="update_billing_company" size="37" value="<?php echo stripslashes($order->billing['company']); ?>" onChange="updateOrdersField('billing_company', encodeURIComponent(this.value))"></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_STREET_ADDRESS; ?></td>
                    <td colspan="3" valign="top" class="dataTableContent"><input name="update_billing_street_address" size="37" value="<?php echo stripslashes($order->billing['street_address']); ?>" onChange="updateOrdersField('billing_street_address', encodeURIComponent(this.value))"></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_SUBURB; ?></td>
                    <td colspan="3" valign="top" class="dataTableContent"><input name="update_billing_suburb" size="37" value="<?php echo stripslashes($order->billing['suburb']); ?>" onChange="updateOrdersField('billing_suburb', encodeURIComponent(this.value))"></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_CITY_STATE; ?></td>
                    <td colspan="2" valign="top" class="dataTableContent" nowrap><input name="update_billing_city" size="15" value="<?php echo stripslashes($order->billing['city']); ?>" onChange="updateOrdersField('billing_city', encodeURIComponent(this.value))">,</td>
                    <td valign="top" class="dataTableContent"><span id="billingStateMenu">
                    <?php echo tep_draw_pull_down_menu('update_billing_zone_id', tep_get_country_zones($order->billing['country_id']), $order->billing['zone_id'], 'style="width: 141px;" onChange="updateOrdersField(\'billing_state\', this.options[this.selectedIndex].text);"');?>
                    </span><span id="billingStateInput"><input name="update_billing_state" size="15" value="<?php echo stripslashes($order->billing['state']); ?>" onChange="updateOrdersField('billing_state', encodeURIComponent(this.value))"></span></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_POST_CODE; ?></td>
                    <td class="dataTableContent" valign="top"><input name="update_billing_postcode" size="5" value="<?php echo $order->billing['postcode']; ?>" onChange="updateOrdersField('billing_postcode', encodeURIComponent(this.value))"></td>
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_COUNTRY; ?></td>
                    <td class="dataTableContent" valign="top">
                    <?php echo tep_draw_pull_down_menu('update_billing_country_id', tep_get_countries(), $order->billing['country_id'], 'style="width: 141px;" onchange="update_zone(\'update_billing_country_id\', \'update_billing_zone_id\', \'billingStateInput\', \'billingStateMenu\'); updateOrdersField(\'billing_country\', this.options[this.selectedIndex].text);"');?>
                    </td>
                  </tr>
                </table>
                </td>
              </tr>
              <!-- billing_address_eof //-->

              <!-- payment_method bof //-->
          <tr>
            <td class="dataTableContent">
            <table cellspacing="0" cellpadding="2" width="100%">
              <tr class="dataTableHeadingRow">
                <td colspan="2" class="dataTableHeadingContent" valign="bottom" title="<?php echo HINT_UPDATE_TO_CC; ?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo ENTRY_PAYMENT_METHOD; ?></td>
                <td></td>
                <td class="dataTableHeadingContent" valign="bottom" title="<?php echo oe_html_no_quote(HINT_UPDATE_CURRENCY);?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo ENTRY_CURRENCY_TYPE;?></td>
                <td></td>
                <td class="dataTableHeadingContent"><?php echo ENTRY_CURRENCY_VALUE; ?></td>
              </tr>
              <tr class="dataTableRow">
                <td colspan="2" class="main">
           <?php
            //START for payment dropdown menu use this by quick_fixer
//              if (ORDER_EDITOR_PAYMENT_DROPDOWN == 'true') {

            // Get list of all payment modules available
            $enabled_payment = array();
            $module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
            $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));

             if ($dir = @dir($module_directory)) {
              while ($file = $dir->read()) {
               if (!is_dir( $module_directory . $file)) {
                if (substr($file, strrpos($file, '.')) == $file_extension) {
                   $directory_array[] = $file;
                 }
               }
             }
            sort($directory_array);
            $dir->close();
           }

          // For each available payment module, check if enabled
          for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) {
          $file = $directory_array[$i];

          include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $file);
          include($module_directory . $file);

          $class = substr($file, 0, strrpos($file, '.'));

          if (tep_class_exists($class)) {
             $module = new $class;

             if ($module->check() > 0) {
              // If module enabled create array of titles
               $enabled_payment[] = array('id' => $module->title, 'text' => $module->title);

              //if the payment method is the same as the payment module title then don't add it to dropdown menu

              if ($module->title == $order->info['payment_method']) {
                  $paymentMatchExists='true';
                 }
              }
            }
          }
        //just in case the payment method found in db is not the same as the payment module title then make it part of the dropdown array or else it cannot be the selected default value
          if ($paymentMatchExists !='true') {
            $enabled_payment[] = array('id' => $order->info['payment_method'], 'text' => $order->info['payment_method']);
           }
            $enabled_payment[] = array('id' => 'Other', 'text' => 'Other');
            //draw the dropdown menu for payment methods and default to the order value
              echo tep_draw_pull_down_menu('update_info_payment_method', $enabled_payment, $order->info['payment_method'], 'id="update_info_payment_method" style="width: 150px;" onChange="init(); updateOrdersField(\'payment_method\', this.options[this.selectedIndex].text)"');



/*
            } 
            else { //draw the input field for payment methods and default to the order value  ?>
           <input name="update_info_payment_method" size="35" value="<?php echo $order->info['payment_method']; ?>" id="update_info_payment_method" onChange="init(); updateOrdersField('payment_method', encodeURIComponent(this.value));">
           <?php } //END for payment dropdown menu use this by quick_fixer ?>
*/
?>
                </td>
                <td width="20">
                </td>
                <td>
             <?php
             ///get the currency info
              reset($currencies->currencies);
              $currencies_array = array();
                while (list($key, $value) = each($currencies->currencies)) {
                      $currencies_array[] = array('id' => $key, 'text' => $value['title']);
                 }
               echo tep_draw_pull_down_menu('update_info_payment_currency', $currencies_array, $order->info['currency'], 'id="update_info_payment_currency" onChange="currency(this.value)"');
?>
                </td>
                <td width="10">
                </td>
                <td>
                    <input name="update_info_payment_currency_value" size="15" readonly="readonly" id="update_info_payment_currency_value" value="<?php echo $order->info['currency_value']; ?>">
                </td>
                </tr>
                  <!-- credit_card bof //-->
                <tr class="dataTableRow">
                  <td colspan="6">
                  <table id="optional"><!--  -->
                    <tr>
                      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
                      <td class="main"><input name="update_info_cc_type" size="32" value="<?php echo $order->info['cc_type']; ?>" onChange="updateOrdersField('cc_type', encodeURIComponent(this.value))"></td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
                      <td class="main"><input name="update_info_cc_owner" size="32" value="<?php echo $order->info['cc_owner']; ?>" onChange="updateOrdersField('cc_owner', encodeURIComponent(this.value))"></td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
                      <td class="main"><input name="update_info_cc_number" size="32" value="<?php echo $order->info['cc_number']; ?>" onChange="updateOrdersField('cc_number', encodeURIComponent(this.value))"></td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
                      <td class="main"><input name="update_info_cc_expires" size="4" value="<?php echo $order->info['cc_expires']; ?>" onChange="updateOrdersField('cc_expires', encodeURIComponent(this.value))"></td>
                    </tr>
                  </table>
                  </td>
                </tr>
              </table>
              </td>
            </tr>
          </table></td>
        </tr>
      </table>
    <div id="productsMessageStack">
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
            <td class="dataTableContent" valign="top"><div align="center"><input type="tel" name="<?php echo "update_products[" . $pID . "][qty]"; ?>" size="2" onKeyUp="updatePrices('qty', '<?php echo $pID; ?>')" onChange="updateProductsField('reload1', '<?php echo $pID; ?>', 'products_quantity', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['qty']; ?>" id="<?php echo "update_products[" . $pID . "][qty]"; ?>"></div></td>
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
            <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $pID . "][model]"; ?>" size="12" onChange="updateProductsField('update', '<?php echo $pID; ?>', 'products_model', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['model']; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][tax]"; ?>" size="5" onKeyUp="updatePrices('tax', '<?php echo $pID; ?>')" onChange="updateProductsField('reload1', '<?php echo $pID; ?>', 'products_tax', encodeURIComponent(this.value))" value="<?php echo tep_display_tax_value($order->products[$i]['tax']); ?>" id="<?php echo "update_products[" . $pID . "][tax]"; ?>">%</td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][price]"; ?>" size="5" onKeyUp="updatePrices('price', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" value="<?php echo @number_format($order->products[$i]['price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $pID . "][price]"; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][final_price]"; ?>" size="5" onKeyUp="updatePrices('final_price', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" value="<?php echo @number_format($order->products[$i]['final_price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $pID . "][final_price]"; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][price_incl]"; ?>" size="5" value="<?php echo number_format(($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1)), 4, '.', ''); ?>" onKeyUp="updatePrices('price_incl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][price_incl]"; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][total_excl]"; ?>" size="5" value="<?php echo number_format($order->products[$i]['final_price'] * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_excl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][total_excl]"; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][total_incl]"; ?>" size="5" value="<?php echo number_format((($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1))) * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_incl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][total_incl]"; ?>"></td>
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
<?php include ("order_editor/templates/totalsBlock.php");?>
      </div>
    </div> <!-- this is end of the master div for the whole totals/shipping area -->

    <div id="historyMessageStack">
        <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>
    <div id="commentsBlock">
<?php include ("order_editor/templates/commentsBlock.php");?>

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
  <?php echo tep_draw_button (oe_html_no_quote(AJAX_SUBMIT_COMMENT),"plus", null,'primary', array('params' => ' onClick="javascript:getNewComment();"'));?>
  </td>
</tr>
  </table>

    <div>
      <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>

    <!-- End of Status Block -->

<?php
  echo '</form>';
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