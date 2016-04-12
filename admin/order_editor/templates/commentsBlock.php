    <table style="border: 1px solid #C9C9C9;" cellspacing="0" cellpadding="2" class="dataTableRow" id="commentsTable">
      <thead>
      <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" align="left"><?= TABLE_HEADING_DELETE ?></td>
        <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
        <td class="dataTableHeadingContent" align="left"><?= TABLE_HEADING_DATE_ADDED ?></td>
        <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
        <td class="dataTableHeadingContent" align="center"><?= TABLE_HEADING_CUSTOMER_NOTIFIED ?></td>
        <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
        <td class="dataTableHeadingContent" align="left"><?= TABLE_HEADING_STATUS ?></td>
        <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
        <td class="dataTableHeadingContent" align="left"><?= TABLE_HEADING_COMMENTS ?></td>
      </tr>
      </thead>
<?php
      $r = 0;
      $orders_history_query = tep_db_query("SELECT orders_status_history_id, orders_status_id, date_added, customer_notified, comments
                                            FROM " . TABLE_ORDERS_STATUS_HISTORY . "
                                            WHERE orders_id = '" . $oID . "'
                                            ORDER BY date_added");
      if (tep_db_num_rows($orders_history_query)) {
?>
      <tbody class="rowOver">
<?php
        while ($orders_history = tep_db_fetch_array($orders_history_query)) {
          $r++;
          $rowClass = ((($r/2) == (floor($r/2))) ? 'dataTableRowOver' : 'dataTableRow');
?>
      <tr class="dataTableRow" id="commentRow<?= $orders_history['orders_status_history_id'] ?>">
        <td class="smallText" align="center"><div id="do_not_delete"><input name="update_comments[<?= $orders_history['orders_status_history_id'] ?>][delete]" type="checkbox" onClick="updateCommentsField('delete', '<?= $orders_history['orders_status_history_id'] ?>', this.checked, '', this)"></div></td>
        <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
        <td class="smallText" align="center"><?= tep_datetime_short($orders_history['date_added']) ?></td>
        <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
<?php
          if ($orders_history['customer_notified'] == '1') {
            $orders_history_icon = tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK);
          }else{
            $orders_history_icon = tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS);
          }
?>
        <td class="smallText" align="center"><?= $orders_history_icon ?></td>
        <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
        <td class="smallText" align="left"><?= $orders_status_array[$orders_history['orders_status_id']] ?></td>
        <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
        <td class="smallText" align="left"><?= tep_draw_textarea_field("update_comments[" . $orders_history['orders_status_history_id'] . "][comments]", "soft", "40", "5", "" .  tep_db_output($orders_history['comments']) . "", "onChange=\"updateCommentsField('update', '" . $orders_history['orders_status_history_id'] . "', 'false', encodeURIComponent(this.value))\"") ?></td>
      </tr>
<?php
        }
?>
      <tbody>
<?php
       } else {
?>
      <tr>
        <td class="smallText" colspan="5"><?= TEXT_NO_ORDER_HISTORY ?></td>
      </tr>
<?php
       }
?>
    </table>
