            <table width="450px" cellpadding="0" style="border: 1px solid #C9C9C9;">
              <!-- billing_address bof //-->
              <tr>
                <td class="dataTableContent">
                <table width="100%">
                  <tr class="dataTableHeadingRow">
                    <td colspan="4" class="dataTableHeadingContent" valign="top"><?php echo ENTRY_BILLING_ADDRESS; ?></td>
                  </tr>

                </table>
                </td>
              </tr>
              <tr id="billingAddressEntry">
                <td class="dataTableContent">
                <table width="100%">
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
<?php
          echo tep_draw_pull_down_menu('update_billing_zone_id', tep_get_country_zones($order->billing['country_id']), $order->billing['zone_id'], 'style="width: 200px;" onChange="updateOrdersField(\'billing_state\', this.options[this.selectedIndex].text);"');
?>
          </span><span id="billingStateInput"><input name="update_billing_state" size="15" value="<?php echo stripslashes($order->billing['state']); ?>" onChange="updateOrdersField('billing_state', encodeURIComponent(this.value))"></span></td>
                  </tr>
                  <tr class="dataTableRow">
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_POST_CODE; ?></td>
                    <td class="dataTableContent" valign="top"><input name="update_billing_postcode" size="5" value="<?php echo $order->billing['postcode']; ?>" onChange="updateOrdersField('billing_postcode', encodeURIComponent(this.value))"></td>
                    <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_COUNTRY; ?></td>
                    <td class="dataTableContent" valign="top">
<?php
          echo tep_draw_pull_down_menu('update_billing_country_id', tep_get_countries(), $order->billing['country_id'], 'style="width: 140px;" onchange="update_zone(\'update_billing_country_id\', \'update_billing_zone_id\', \'billingStateInput\', \'billingStateMenu\'); updateOrdersField(\'billing_country\', this.options[this.selectedIndex].text);"');
?>
                    </td>
                  </tr>
                </table>
                </td>
              </tr>
              <!-- billing_address_eof //-->
              <!-- payment_method bof //-->
<?php /*
              <tr>
                <td class="dataTableContent">

      <table width="100%">
        <tr class="dataTableHeadingRow">
          <td colspan="2" class="dataTableHeadingContent" valign="bottom"><?php echo ENTRY_PAYMENT_METHOD; ?>
          </td>

         <td></td>
           <td class="dataTableHeadingContent" valign="bottom">
             </td>
           <td></td>
           <td class="dataTableHeadingContent"><?php echo ENTRY_CURRENCY_VALUE; ?></td>
         </tr>

       <tr class="dataTableRow">
         <td colspan="2" class="main">
         <?php
          //START for payment dropdown menu use this by quick_fixer
            if (ORDER_EDITOR_PAYMENT_DROPDOWN == 'true') {

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
        }  else { //draw the input field for payment methods and default to the order value  ?>

       <input name="update_info_payment_method" size="35" value="<?php echo $order->info['payment_method']; ?>" id="update_info_payment_method" onChange="init(); updateOrdersField('payment_method', encodeURIComponent(this.value));">

       <?php } //END for payment dropdown menu use this by quick_fixer ?>

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

         <td width="10">fff</td>

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
<!-- credit_card eof //-->

    </table>

        </td>
              </tr>
*/
?>
            </table>