        <table border="0" width="100%" cellspacing="0" cellpadding="2" id="productsTable">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent"><div align="center"><?php echo TABLE_HEADING_DELETE; ?></div></td>
            <td class="dataTableHeadingContent"><div align="center"><?php echo TABLE_HEADING_QUANTITY; ?></div></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_BASE_PRICE;?>"><span id="icon-info-curency" class="ui-icon ui-icon-info ui-icon-info"></span><?php echo TABLE_HEADING_BASE_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_PRICE_EXCL;?>"><span id="icon-info-curency" class="ui-icon ui-icon-info ui-icon-info"></span><?php echo TABLE_HEADING_UNIT_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_PRICE_INCL;?>"><span id="icon-info-curency" class="ui-icon ui-icon-info ui-icon-info"></span><?php echo TABLE_HEADING_UNIT_PRICE_TAXED;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_TOTAL_EXCL;?>"><span id="icon-info-curency" class="ui-icon ui-icon-info ui-icon-info"></span><?php echo TABLE_HEADING_TOTAL_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_TOTAL_INCL;?>"><span id="icon-info-curency" class="ui-icon ui-icon-info ui-icon-info"></span><?php echo TABLE_HEADING_TOTAL_PRICE_TAXED;?></td>
          </tr>
<?php
  if (sizeof($order->products)) {
    for ($i=0; $i<sizeof($order->products); $i++) {
      $pID = $order->products[$i]['orders_products_id'];  ?>
          <tr class="dataTableRow border_bottom">
            <td class="dataTableContent" valign="top"><div align="center"><input type="checkbox" name="<?php echo "update_products[" . $pID . "][delete]"; ?>" onClick="updateProductsField('delete', '<?php echo $pID; ?>', 'delete', this.checked, this)"></div></td>
            <td class="dataTableContent" valign="top"><div align=""><input type="tel" name="<?php echo "update_products[" . $pID . "][qty]"; ?>" size="2" onKeyUp="updatePrices('qty', '<?php echo $pID; ?>')" onChange="updateProductsField('reload1', '<?php echo $pID; ?>', 'products_quantity', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['qty']; ?>" id="<?php echo "update_products[" . $pID . "][qty]"; ?>"></div></td>
            <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $pID . "][name]"; ?>" size="50" onChange="updateProductsField('update', '<?php echo $pID; ?>', 'products_name', encodeURIComponent(this.value))" value='<?php echo oe_html_quotes($order->products[$i]['name']); ?>'>
    <?php
      // Has Attributes?
     if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
          $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
                echo '<br><nobr><small>&nbsp;<i> - ' . "<input name='update_products[$pID][attributes][$orders_products_attributes_id][option]' size='6' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['option']) . "' onChange=\"updateAttributesField('simple', 'products_options', '" . $orders_products_attributes_id . "', '" . $pID . "', encodeURIComponent(this.value))\">" . ': ' . "<input name='update_products[$pID][attributes][$orders_products_attributes_id][value]' size='10' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['value']) . "' onChange=\"updateAttributesField('simple', 'products_options_values', '" . $orders_products_attributes_id . "', '" . $pID . "', encodeURIComponent(this.value))\">" . ': ' . "</i><input name='update_products[$pID][attributes][$orders_products_attributes_id][prefix]' size='1' id='p" . $pID . "_" . $orders_products_attributes_id . "_prefix' value='" . $order->products[$i]['attributes'][$j]['prefix'] . "' onKeyUp=\"updatePrices('att_price', '" . $pID . "')\" onChange=\"updateAttributesField('hard', 'price_prefix', '" . $orders_products_attributes_id . "', '" . $pID . "', encodeURIComponent(this.value))\">" . ': ' . "<input name='update_products[$pID][attributes][$orders_products_attributes_id][price]' size='7' value='" . $order->products[$i]['attributes'][$j]['price'] . "' onKeyUp=\"updatePrices('att_price', '" . $pID . "')\" onChange=\"updateAttributesField('hard', 'options_values_price', '" . $orders_products_attributes_id . "', '" . $pID . "', encodeURIComponent(this.value))\" id='p". $pID . "a" . $orders_products_attributes_id . "'>";

                echo '</small></nobr>';
            }  //end for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {

             //Has downloads?

    if (DOWNLOAD_ENABLED == 'true') {
   $downloads_count = 1;
   $d_index = 0;
   $download_query_raw ="SELECT orders_products_download_id, orders_products_filename, download_maxdays, download_count
                         FROM orders_products_download
                         WHERE orders_products_id='" . $pID . "'
                         AND orders_id='" . $oID . "'
                         ORDER BY orders_products_download_id";

        $download_query = tep_db_query($download_query_raw);

        //
        if (isset($downloads->products)) unset($downloads->products);
        //

        if (tep_db_num_rows($download_query) > 0) {
        while ($download = tep_db_fetch_array($download_query)) {

        $downloads->products[$d_index] = array(
                    'id' => $download['orders_products_download_id'],
                    'filename' => $download['orders_products_filename'],
                    'maxdays' => $download['download_maxdays'],
                    'maxcount' => $download['download_count']);

        $d_index++;

        }
       }

   if (isset($downloads->products) && (sizeof($downloads->products) > 0)) {
    for ($mm=0; $mm<sizeof($downloads->products); $mm++) {
    $id =  $downloads->products[$mm]['id'];
    echo '<br><small>';
    echo '<nobr>' . ENTRY_DOWNLOAD_COUNT . $downloads_count . "";
    echo ' </nobr><br>' . "\n";


      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_FILENAME . ": <input name='update_downloads[" . $id . "][filename]' size='12' value='" . $downloads->products[$mm]['filename'] . "' onChange=\"updateDownloads('orders_products_filename', '" . $id . "', '" . $pID . "', this.value)\">";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXDAYS . ": <input name='update_downloads[" . $id . "][maxdays]' size='6' value='" . $downloads->products[$mm]['maxdays'] . "' onChange=\"updateDownloads('download_maxdays', '" . $id . "', '" . $pID . "', this.value)\">";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXCOUNT . ": <input name='update_downloads[" . $id . "][maxcount]' size='6' value='" . $downloads->products[$mm]['maxcount'] . "' onChange=\"updateDownloads('download_count', '" . $id . "', '" . $pID . "', this.value)\">";


     echo ' </nobr>' . "\n";
     echo '<br></small>';
     $downloads_count++;
     } //end  for ($mm=0; $mm<sizeof($download_query); $mm++) {
    }
   } //end download
  } //end if (sizeof($order->products[$i]['attributes']) > 0) {
?>
            </td>
            <td class="dataTableContent" valign="top"><input style="text-align:center;" name="<?php echo "update_products[" . $pID . "][model]"; ?>" size="12" onChange="updateProductsField('update', '<?php echo $pID; ?>', 'products_model', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['model']; ?>"></td>
            <td class="dataTableContent" valign="top"><input style="text-align:center;" name="<?php echo "update_products[" . $pID . "][tax]"; ?>" size="5" onKeyUp="updatePrices('tax', '<?php echo $pID; ?>')" onChange="updateProductsField('reload1', '<?php echo $pID; ?>', 'products_tax', encodeURIComponent(this.value))" value="<?php echo tep_display_tax_value($order->products[$i]['tax']); ?>" id="<?php echo "update_products[" . $pID . "][tax]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][price]"; ?>" size="7" onKeyUp="updatePrices('price', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" value="<?php echo @number_format($order->products[$i]['price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $pID . "][price]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][final_price]"; ?>" size="7" onKeyUp="updatePrices('final_price', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" value="<?php echo @number_format($order->products[$i]['final_price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $pID . "][final_price]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][price_incl]"; ?>" size="7" value="<?php echo number_format(($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1)), 4, '.', ''); ?>" onKeyUp="updatePrices('price_incl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][price_incl]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][total_excl]"; ?>" size="8" value="<?php echo number_format($order->products[$i]['final_price'] * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_excl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][total_excl]"; ?>"></td>
            <td class="dataTableContent" valign="top" align="center"><input style="text-align:right;" type="tel" name="<?php echo "update_products[" . $pID . "][total_incl]"; ?>" size="8" value="<?php echo number_format((($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1))) * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_incl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][total_incl]"; ?>"></td>
          </tr>

<?php
    }
  } else {
    //the order has no products
?>
          <tr class="dataTableRow">
            <td colspan="10" class="dataTableContent" valign="middle" align="center" style="padding: 20px 0 20px 0;"><?php echo TEXT_NO_ORDER_PRODUCTS; ?></td>
          </tr>
          <tr class="dataTableRow">
            <td colspan="10" style="border-bottom: 1px solid #C9C9C9;"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
          </tr>
<?php
  }
?>
        </table>