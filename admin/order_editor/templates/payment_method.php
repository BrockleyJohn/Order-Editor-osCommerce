                  <!-- payment_method bof //-->
            <table width="380" border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" valign="bottom" title="<?= HINT_UPDATE_TO_CC ?>">
                  <span style="display:block;" id="icon-info-totals" class="ui-icon ui-icon-info ui-icon-white">
                  </span><?= ENTRY_PAYMENT_METHOD ?></td>
                <td class="dataTableHeadingContent" valign="bottom" title="<?= HINT_UPDATE_CURRENCY ?>">
                  <span style="display:block;"  id="icon-info-curency" class="ui-icon ui-icon-info ui-icon-white">
                  </span><?= ENTRY_CURRENCY_TYPE ?></td>
                <td class="dataTableHeadingContent"><?php echo ENTRY_CURRENCY_VALUE; ?></td>
              </tr>
              <tr>
                  <tr class="dataTableRow">
                    <td class="main">
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
    ?>
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
                    <td>
                        <input name="update_info_payment_currency_value" size="10" readonly="readonly" id="update_info_payment_currency_value" value="<?php echo $order->info['currency_value']; ?>">
                    </td>
                  </tr>
                      <!-- credit_card bof //-->
                  <tr class="dataTableRow">
                    <td colspan="3">
                    <table id="optional" style="display:none;"><!--  -->
                      <tr><br /></tr>
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

              </tr>
            </table>
                <!-- payment_method eof //-->
