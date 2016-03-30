<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
        break;
    }
  }
  require(DIR_WS_INCLUDES . 'template_top.php');

//  include('order_editor/css.php');
      //because if you haven't got your css, what have you got?

//  include('order_editor/javascript.php');
      //because if you haven't got your javascript, what have you got?
?>
   
    <div id="header">
      <p id="headerTitle" class="pageHeading"><?php echo sprintf(HEADING_TITLE, $oID, tep_datetime_short($order->info['date_purchased'])); ?></p>
<?php
 echo      tep_draw_button(IMAGE_EDIT, 'document', tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('oID', 'action')) . 'oID=' . $_GET['oID'] . '&action=edit'));
 echo      tep_draw_button(IMAGE_ORDERS_INVOICE, 'document', tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $_GET['oID']) . '" TARGET="_blank"');
 echo      tep_draw_button(IMAGE_ORDERS_PACKINGSLIP, 'document', tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $_GET['oID']) . '" TARGET="_blank"');
 ?>
    </div>
<div id="ordersMessageStack"></div>
    <div>
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
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo "Nick:"; ?></td>
              <td colspan="3" valign="top" class="dataTableContent"><input name="update_customer_nick" size="37" value="<?php echo stripslashes($order->customer['nick']); ?>" onChange="updateOrdersField('customers_nick', encodeURIComponent(this.value))"></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo "NIF:"; ?></td>
              <td colspan="3" valign="top" class="dataTableContent"><input name="update_customer_nif" size="37" value="<?php echo stripslashes($order->billing['nif']); ?>" onChange="updateOrdersField('billing_nif', encodeURIComponent(this.value))"></td>
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
                <?php 
                echo tep_draw_pull_down_menu('update_customer_zone_id', tep_get_country_zones($order->customer['country_id']), $order->customer['zone_id'], 'style="width: 200px;" onChange="updateOrdersField(\'customers_state\', this.options[this.selectedIndex].text);"');
                ?></span><span id="customerStateInput"><input name="update_customer_state" size="15" value="<?php echo stripslashes($order->customer['state']); ?>" onChange="updateOrdersField('customers_state', encodeURIComponent(this.value))"></span></td>
            </tr>
            <tr class="dataTableRow">
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_POST_CODE; ?></td>
              <td class="dataTableContent" valign="top"><input name="update_customer_postcode" size="5" value="<?php echo $order->customer['postcode']; ?>" onChange="updateOrdersField('customers_postcode', encodeURIComponent(this.value))"></td>
              <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_COUNTRY; ?></td>
              <td class="dataTableContent" valign="top">
<?php
                echo '                ' . tep_draw_pull_down_menu('update_customer_country_id', tep_get_countries(), $order->customer['country_id'], 'style="width: 200px;" onChange="update_zone(\'update_customer_country_id\', \'update_customer_zone_id\', \'customerStateInput\', \'customerStateMenu\'); updateOrdersField(\'customers_country\', this.options[this.selectedIndex].text);"');
                 ?>

              </td>
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

    </div>
 

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
