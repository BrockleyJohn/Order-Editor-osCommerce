        <table width="100%">
          <tr><td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="valign-bottom">
<?php
  if (sizeof($shipping_quotes) > 0) {
?>
                <!-- shipping_quote bof //-->
                <table border="0" width="550" cellspacing="0" cellpadding="2" style="border: 1px solid #C9C9C9;">
                  <thead>
                  <tr class="dataTableHeadingRow">
                    <th class="dataTableHeadingContent" colspan="3"><?= TABLE_HEADING_SHIPPING_QUOTES ?></th>
                  </tr>
                  <thead>
<?php
    $r = 0;
    for ($i=0, $n=sizeof($shipping_quotes); $i<$n; $i++) {
      for ($j=0, $n2=sizeof($shipping_quotes[$i]['methods']); $j<$n2; $j++) {
        $r++;
        if (!isset($shipping_quotes[$i]['tax'])) $shipping_quotes[$i]['tax'] = 0;
//        $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
?>
                  <tbody class="rowOver" id= "shipping_quote">
                  <tr class="dataTableRow" onClick="selectRowEffect(this, <?= $r ?>); setShipping(<?= $r ?>);">
                    <td class="dataTableContent align-left valign-top" width="15px">
                      <input type="radio" name="shipping" id="shipping_radio_<?= $r ?>" value="<?= $shipping_quotes[$i]['id'] ?>_<?= $shipping_quotes[$i]['methods'][$j]['id'] ?>">
                      <input type="hidden" id="update_shipping[<?= $r ?>][title]" name="update_shipping[<?= $r ?>][title]" value="<?= $shipping_quotes[$i]['module'] ?> (<?= $shipping_quotes[$i]['methods'][$j]['title'] ?>):">
                      <input type="hidden" id="update_shipping[<?= $r ?>][value]" name="update_shipping[<?= $r ?>][value]" value="<?= tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']) ?>">
                      <input type="hidden" id="update_shipping[<?= $r ?>][id]" name="update_shipping[<?= $r ?>][id]" value="<?= $shipping_quotes[$i]['id'] ?>_<?= $shipping_quotes[$i]['methods'][$j]['id'] ?>">
                    </td>
                    <td class="dataTableContent valign-top"><?= $shipping_quotes[$i]['module'] ?> (<?= $shipping_quotes[$i]['methods'][$j]['title'] ?>):</td>
                    <td class="dataTableContent align-right"><?= $currencies->format(tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) ?></td>
                  </tr>
                  </tbody>
<?php
      }
    }
?>
                  <tfoot>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" colspan="3"><?= sprintf(TEXT_PACKAGE_WEIGHT_COUNT, $shipping_num_boxes . ' x ' . $shipping_weight, $total_count) ?></td>
                  </tr>
                   </tfoot>
                </table>
                <!-- shipping_quote_eof //-->
<?php
  } else {
  echo AJAX_NO_QUOTES;
  }
?>
                </td>
                <!-- order_totals bof //-->
                <br />
                <td class="dataTableRow align-right valign-top" rowspan="2" nowrap  style="border: 1px solid #C9C9C9;width:10%;">
                  <table id="totals_table" cellspacing="0" cellpadding="2">
                    <thead>
                    <tr class="dataTableHeadingRow">
                      <th class="dataTableHeadingContent" colspan="2" title="<?= HINT_TOTALS ?>">
                      <span style="display:block;" id="icon-info-totals" class="ui-icon ui-icon-info ui-icon-white">
                      </span><?= TABLE_HEADING_OT_TOTALS ?></th>
                      <th class="dataTableHeadingContent" colspan="2" nowrap><?= TABLE_HEADING_OT_VALUES ?></th>
                    </tr>
                    </thead>
                    <tbody class="rowOver">
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

// Here we could add an array of classes that can be modified by hand
// or exclude the ones that can't (ot_subtotal, ot_total
// You can also add a class to the if () sentence to allor
// that class to be editable.
    if (($order->totals[$i]['class'] == 'ot_total') || ($order->totals[$i]['class'] == 'ot_subtotal') || ($order->totals[$i]['class'] == 'ot_tax') || ($order->totals[$i]['class'] == 'ot_loworderfee') ) {
?>
                    <tr class="dataTableRow">
<?php
      if ($order->totals[$i]['class'] != 'ot_total') {
?>
                    <td class="dataTableContent valign-middle" height="15">
                      <span id="update_totals[<?= $i ?>]">
                        <a href="javascript:setCustomOTVisibility('update_totals[<?= ($i+1) ?>]', 'visible', 'update_totals[<?= $i ?>]');">
                          <span title="<?= IMAGE_ADD_NEW_OT ?>" id="icon-add" class="ui-icon ui-icon-circle-plus"></span>
                        </a>
                      </span>
                    </td>
<?php
      } else {
?>
                      <td class="dataTableContent valign-middle">&nbsp;</td>
<?php
      }
?>
                      <td class="dataTableContent align-right">
                        <input name="update_totals[<?= $i ?>][title]" value="<?= trim($order->totals[$i]['title']) ?>" readonly="readonly">
                      </td>
<?php
      if ($order->info['currency'] != DEFAULT_CURRENCY) {
?>
                      <td class="dataTableContent">&nbsp;</td>
<?php
      }
?>
                      <td class="dataTableContent align-right" nowrap>
                        <?= $order->totals[$i]['text'] ?>
                        <input name="update_totals[<?= $i ?>][value]" type="hidden" value="<?= number_format (floatval($order->totals[$i]['value']), 2, '.', '') ?>">
                        <input name="update_totals[<?= $i ?>][class]" type="hidden" value="<?= $order->totals[$i]['class'] ?>">
                      </td>
                    </tr>
<?php
    } else { // si no es subtotal, total, tax o loworder_fee
      if ($i % 2) {
?>
                <tr class="dataTableRow" id="update_totals[<?= $i ?>]" style="visibility: hidden; display: none;">
                  <td class="dataTableContent valign-middle" height="15">
                    <a href='javascript:setCustomOTVisibility("update_totals[<?= $i ?>]", "hidden", "update_totals[<?= ($i-1) ?>]")'>
                      <span title="<?= IMAGE_REMOVE_NEW_OT ?>" id="icon-remove" class="ui-icon ui-icon-circle-minus ui-icon-red"></span>
                    </a>
                  </td>
<?php
      } else {
?>
                  <tr class="dataTableRow">
                    <td class="dataTableContent valign-middle" height="15">
                      <span id="update_totals[<?= $i ?>]">
                      <a href="javascript:setCustomOTVisibility('update_totals[<?= ($i+1) ?>]', 'visible', 'update_totals[<?= $i ?>]');">
                        <span title="<?= IMAGE_ADD_NEW_OT ?>" id="icon-add2" class="ui-icon ui-icon-circle-plus"></span>
                      </a>
                      </span>
                    </td>
<?php
      }
?>
                    <td class="dataTableContent align-right">
                      <input name="update_totals[<?= $i ?>][title]" id="<?= $id ?>[title]" value="<?= trim($order->totals[$i]['title']) ?>" onChange="obtainTotals()">
                    </td>
                    <td class="dataTableContent align-right">
                      <input class="align-right" name="update_totals[<?= $i ?>][value]" id="<?= $id ?>[value]" value="<?= number_format (floatval($order->totals[$i]['value']), 2, '.', '') ?>" size="6" onChange="obtainTotals()">
                      <input name="update_totals[<?= $i ?>][class]" type="hidden" value="<?= $order->totals[$i]['class'] ?>">
                      <input name="update_totals[<?= $i ?>][id]" type="hidden" value="<?= $shipping_module_id ?>" id="<?= $id ?>[id]">
                    </td>
<?php

      if ($order->info['currency'] != DEFAULT_CURRENCY) {
?>
                    <td class="dataTableContent align-right" nowrap><?= $order->totals[$i]['text'] ?></td>
<?php
      }
?>
                  </tr>

<?php
    }
  }
?>
                    </tbody>

                </table>
              </td>
                <!-- order_totals_eof //-->
              </tr>
            </table>
          </td></tr>
        </table>
