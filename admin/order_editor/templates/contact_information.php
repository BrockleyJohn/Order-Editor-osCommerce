          <table width="380" border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
            <tr class="dataTableHeadingRow">
              <td colspan="4" class="dataTableHeadingContent" valign="top"><?php echo CONTACT_INFORMATION; ?></td>
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