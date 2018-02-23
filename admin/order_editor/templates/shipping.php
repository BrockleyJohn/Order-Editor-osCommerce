          <table width="380" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #C9C9C9;">
            <tr class="dataTableHeadingRow">
              <td colspan="4" class="dataTableHeadingContent" valign="top" title="<?php echo HINT_SHIPPING_ADDRESS;?>"><span id="icon-info-shipping" class="ui-icon ui-icon-info ui-icon-white"></span><?php echo ENTRY_SHIPPING_ADDRESS;?></td>
            </tr>
            <tr id="shippingAddressEntry">
              <td class="dataTableContent">
              <table width="100%" cellspacing="0" cellpadding="2">
                <tr class="dataTableRow">
                  <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_NAME; ?></td>
                  <td colspan="3" valign="top" class="dataTableContent"><input name="update_delivery_name" size="37" value="<?php echo stripslashes($order->delivery['name']); ?>" onChange="updateOrdersField('delivery_name', encodeURIComponent(this.value))"></td>
                </tr>
                <tr class="dataTableRow">
                  <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_COMPANY; ?></td>
                  <td colspan="3" valign="top" class="dataTableContent"><input name="update_delivery_company" size="37" value="<?php echo stripslashes($order->delivery['company']); ?>" onChange="updateOrdersField('delivery_company', encodeURIComponent(this.value))"></td>
                </tr>
                <tr class="dataTableRow">
                  <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_STREET_ADDRESS; ?></td>
                  <td colspan="3" valign="top" class="dataTableContent"><input name="update_delivery_street_address" size="37" value="<?php echo stripslashes($order->delivery['street_address']); ?>" onChange="updateOrdersField('delivery_street_address', encodeURIComponent(this.value))"></td>
                </tr>
                <tr class="dataTableRow">
                  <td class="dataTableContent" valign="middle" align="right" nowrap><?php echo ENTRY_SUBURB; ?></td>
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