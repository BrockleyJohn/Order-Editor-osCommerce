          <table width="380" border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
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
          </table>
