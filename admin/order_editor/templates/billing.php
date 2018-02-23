            <table width="380" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #C9C9C9;">
              <tr class="dataTableHeadingRow">
                <td colspan="4" class="dataTableHeadingContent" valign="top"><?php echo ENTRY_BILLING_ADDRESS; ?></td>
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
            </table>
                  <!-- billing_address_eof //-->
