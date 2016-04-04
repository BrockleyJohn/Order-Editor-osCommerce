        <table width="100%">
          <tr><td>
            <table width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top" width="100%">
                  <br>
                  <div>
<?php /*
                    <a href="<?php echo tep_href_link('edit_orders_add_product.php', 'oID=' . $oID . '&step=1'); ?>" target="addProducts" onClick="openWindow('<?php echo tep_href_link('edit_orders_add_product.php', 'oID=' . $oID . '&step=1'); ?>','addProducts');return false"><?php echo tep_image_button('button_add_article.gif', TEXT_ADD_NEW_PRODUCT);
                      ?></a>
*/
?>
                    <?php echo tep_draw_button(TEXT_ADD_NEW_PRODUCT,'circle-plus',"javascript:openWindow('".tep_href_link('edit_orders_add_product.php', 'oID=' . $oID . '&step=1')."','addProducts');",'secondary'); ?><input type="hidden" name="subaction" value="">
                      <input type="hidden" name="subaction" value="">
                  </div>
                  <br>
                </td>
        <!-- order_totals bof //-->
                <td align="right" rowspan="2" valign="top" nowrap class="dataTableRow" style="border: 1px solid #C9C9C9;">
                  <table cellspacing="0" cellpadding="2">
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent" width="15" colspan="2" nowrap title="<?php echo HINT_TOTALS;?>"><img src="images/icon_info.gif" border="0" width="13" height="13">
                      <?php echo TABLE_HEADING_OT_TOTALS; ?></td>
                      <td class="dataTableHeadingContent" colspan="2" nowrap><?php echo TABLE_HEADING_OT_VALUES; ?></td>
                    </tr>
<?php
  for ($i=0; $i<sizeof($order->totals); $i++) {

    $id = $order->totals[$i]['class'];

    if ($order->totals[$i]['class'] == 'ot_shipping') {
       if (tep_not_null($order->info['shipping_id'])) {
           $shipping_module_id = $order->info['shipping_id'];
           } else {
           //here we could create logic to attempt to determine the shipping module used if it's not in the database
           $shipping_module_id = '';
           }
      } else {
        $shipping_module_id = '';
      } //end if ($order->totals[$i]['class'] == 'ot_shipping') {

    $rowStyle = (($i % 2) ? 'dataTableRowOver' : 'dataTableRow');
    if ( ($order->totals[$i]['class'] == 'ot_total') || ($order->totals[$i]['class'] == 'ot_subtotal') || ($order->totals[$i]['class'] == 'ot_tax') || ($order->totals[$i]['class'] == 'ot_loworderfee') ) {
?>
                    <tr class="<?php echo $rowStyle;?>">
<?php
      if ($order->totals[$i]['class'] != 'ot_total') {
?>
        <td class="dataTableContent" valign="middle" height="15">
          <span id="update_totals[<?php echo $i;?>]">
            <a href='javascript:setCustomOTVisibility("update_totals[<?php echo ($i+1);?>]","visible", "update_totals[<?php echo $i;?>]")'>
              <span title="<?php echo IMAGE_ADD_NEW_OT;?>" id="icon-add" class="ui-icon ui-icon-circle-plus"></span>
            </a>
          </span>
<?php
      } else {
?>
                      <td class="dataTableContent" valign="middle">&nbsp;</td>
<?php
      }
?>
                      <td align="right" class="dataTableContent"><input name="update_totals[<?php echo $i;?>][title]" value="<?php echo trim($order->totals[$i]['title']);?>" readonly="readonly"></td>
<?php
      if ($order->info['currency'] != DEFAULT_CURRENCY) {
?>
                      <td class="dataTableContent">&nbsp;</td>
<?php
      }
?>
                      <td align="rightf" class="dataTableContent" nowrap><?php echo $order->totals[$i]['text'];?><input name="update_totals[<?php echo $i;?>][value]" type="hidden" value="<?php echo number_format (floatval($order->totals[$i]['value']), 2, '.', '');?>"><input name="update_totals[<?php echo $i;?>][class]" type="hidden" value="<?php echo $order->totals[$i]['class'];?>"></td>
                    </tr>
<?php
    } else {
      if ($i % 2) {
?>
                          <tr class="<?php echo $rowStyle;?>" id="update_totals[<?php echo $i;?>]" style="visibility: hidden; display: none;">
                            <td class="dataTableContent" valign="middle" height="15">
                              <a href='javascript:setCustomOTVisibility("update_totals[<?php echo $i;?>]", "hidden", "update_totals[<?php echo ($i-1);?>]")'>
                              <?php echo tep_image('order_editor/images/minus.gif', IMAGE_REMOVE_NEW_OT);?>
                              </a>
                            </td>
<?php
      } else {
?>
                  <tr class="<?php echo $rowStyle;?>">
                    <td class="dataTableContent" valign="middle" height="15">
                      <span id="update_totals[<?php echo $i;?>]">
                      <a href="javascript:setCustomOTVisibility('update_totals[<?php echo ($i+1);?>]', 'visible', 'update_totals[<?php echo $i;?>]');">
                       <img src="order_editor/images/plus.gif" border="0" alt="<?php echo IMAGE_ADD_NEW_OT;?>" title="<?php echo IMAGE_ADD_NEW_OT;?>">
                      </a>
                      </span>
                    </td>
<?php
      }

      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '" onChange="obtainTotals()"></td>' . "\n" .
           '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format (floatval($order->totals[$i]['value']), 2, '.', '') . '" size="6" onChange="obtainTotals()"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td align="rights" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '</td>' . "\n";
      echo '                  </tr>' . "\n";
    }
  }
?>
                </table>
              </td>
                <!-- order_totals_eof //-->
              </tr>
              <tr>
                <td valign="bottom">
<?php
  if (sizeof($shipping_quotes) > 0) {
?>
                <!-- shipping_quote bof //-->
                <table width="550" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo TABLE_HEADING_SHIPPING_QUOTES; ?></td>
                  </tr>
<?php
    $r = 0;
    for ($i=0, $n=sizeof($shipping_quotes); $i<$n; $i++) {
      for ($j=0, $n2=sizeof($shipping_quotes[$i]['methods']); $j<$n2; $j++) {
        $r++;
        if (!isset($shipping_quotes[$i]['tax'])) $shipping_quotes[$i]['tax'] = 0;
        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
?>
                  <tr class="<?php echo $rowClass;?>" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, '<?php echo $rowClass;?>')" onClick="selectRowEffect(this, <?php echo $r;?>); setShipping(<?php echo $r;?>);">
                    <td class="dataTableContent" valign="top" align="left" width="15px">
                      <input type="radio" name="shipping" id="shipping_radio_<?php echo $r;?>" value="<?php echo $shipping_quotes[$i]['id'];?>_<?php echo $shipping_quotes[$i]['methods'][$j]['id'];?>">
                      <input type="hidden" id="update_shipping[<?php echo $r;?>][title]" name="update_shipping[<?php echo $r?>][title]" value="<?php echo $shipping_quotes[$i]['module'];?> (<?php echo $shipping_quotes[$i]['methods'][$j]['title'];?>):">
                      <input type="hidden" id="update_shipping[<?php echo $r;?>][value]" name="update_shipping[<?php echo $r;?>][value]" value="<?php echo tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']);?>">
                      <input type="hidden" id="update_shipping[<?php echo $r;?>][id]" name="update_shipping[<?php echo $r;?>][id]" value="<?php echo $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'];?>">
                    </td>
                    <td class="dataTableContent" valign="top"><?php echo $shipping_quotes[$i]['module'];?> (<?php echo $shipping_quotes[$i]['methods'][$j]['title'];?>):</td>
                    <td class="dataTableContent" align="right"><?php echo $currencies->format(tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']);?></td>
                  </tr>
<?php
      }
    }
?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?php echo sprintf(TEXT_PACKAGE_WEIGHT_COUNT, $shipping_num_boxes . ' x ' . $shipping_weight, $total_count); ?></td>
                  </tr>
                </table>
                <!-- shipping_quote_eof //-->
<?php
  } else {
  echo AJAX_NO_QUOTES;
  }
?>                </td>
              </tr>
            </table>

          </td></tr>
         </table>
