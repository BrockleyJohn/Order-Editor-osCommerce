  <div width="100%" style="border: 1px solid #C9C9C9;">
 
    <!-- product_listing bof //-->

        <table width="100%" id="productsTable">
         <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DELETE; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_QUANTITY; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX; ?></td>
            <td class="dataTableHeadingContent"><?php  echo TABLE_HEADING_BASE_PRICE; ?> </td>
            <td class="dataTableHeadingContent"><?php  echo TABLE_HEADING_UNIT_PRICE; ?> </td>
            <td class="dataTableHeadingContent"><?php  echo TABLE_HEADING_UNIT_PRICE_TAXED; ?> </td>
            <td class="dataTableHeadingContent"><?php  echo TABLE_HEADING_TOTAL_PRICE; ?> </td>
            <td class="dataTableHeadingContent"><?php  echo TABLE_HEADING_TOTAL_PRICE_TAXED; ?> </td>
          </tr>
  <?php
  if (sizeof($order->products)) {
    for ($i=0; $i<sizeof($order->products); $i++) {
      $orders_products_id = $order->products[$i]['orders_products_id'];  ?>

          <tr class="dataTableRow">
            <td class="dataTableContent" valign="top"><div align="center"><input type="checkbox" name="<?php echo "update_products[" . $orders_products_id . "][delete]"; ?>" onClick="updateProductsField('delete', '<?php echo $orders_products_id; ?>', 'delete', this.checked, this)"></div></td>

            <td class="dataTableContent" valign="top"><div align="center"><input name="<?php echo "update_products[" . $orders_products_id . "][qty]"; ?>" size="2" onKeyUp="updatePrices('qty', '<?php echo $orders_products_id; ?>')" onChange="updateProductsField('reload1', '<?php echo $orders_products_id; ?>', 'products_quantity', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['qty']; ?>" id="<?php echo "update_products[" . $orders_products_id . "][qty]"; ?>"></div></td>

            <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $orders_products_id . "][name]"; ?>" size="50" onChange="updateProductsField('update', '<?php echo $orders_products_id; ?>', 'products_name', encodeURIComponent(this.value))" value='<?php echo oe_html_quotes($order->products[$i]['name']); ?>'>

  <?php
      // Has Attributes?
     if (isset($order->products[$i]['attributes']) && (sizeof($order->products[$i]['attributes']) > 0)) {
        for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
          $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
        echo '<br><nobr><small>&nbsp;<i><b> - ' . 
        "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' size='6' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['option']) . "' onChange=\"updateAttributesField('simple', 'products_options', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' . 
        
        "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' size='10' value='" . oe_html_quotes($order->products[$i]['attributes'][$j]['value']) . "' onChange=\"updateAttributesField('simple', 'products_options_values', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' .
        
        "</b></i>".
        
        "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][prefix]' size='1' id='p" . $orders_products_id . "_" . $orders_products_attributes_id . "_prefix' value='" . $order->products[$i]['attributes'][$j]['prefix'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\" onChange=\"updateAttributesField('hard', 'price_prefix', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\">" . ': ' . 
        
        "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' size='7' value='" . $order->products[$i]['attributes'][$j]['price'] . "' onKeyUp=\"updatePrices('att_price', '" . $orders_products_id . "')\" onChange=\"updateAttributesField('hard', 'options_values_price', '" . $orders_products_attributes_id . "', '" . $orders_products_id . "', encodeURIComponent(this.value))\" id='p". $orders_products_id . "a" . $orders_products_attributes_id . "'>";

        echo '</small></nobr>';
      }  //end for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {

       //Has downloads?

    if (DOWNLOAD_ENABLED == 'true') {
   $downloads_count = 1;
   $d_index = 0;
   $download_query_raw ="SELECT orders_products_download_id, orders_products_filename, download_maxdays, download_count
                         FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
             WHERE orders_products_id='" . $orders_products_id . "'
             AND orders_id='" . (int)$oID . "'
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


      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_FILENAME . ": <input name='update_downloads[" . $id . "][filename]' size='12' value='" . $downloads->products[$mm]['filename'] . "' onChange=\"updateDownloads('orders_products_filename', '" . $id . "', '" . $orders_products_id . "', this.value)\">";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXDAYS . ": <input name='update_downloads[" . $id . "][maxdays]' size='6' value='" . $downloads->products[$mm]['maxdays'] . "' onChange=\"updateDownloads('download_maxdays', '" . $id . "', '" . $orders_products_id . "', this.value)\">";
      echo ' </nobr><br>' . "\n";
      echo '<nobr>&nbsp;- ' . ENTRY_DOWNLOAD_MAXCOUNT . ": <input name='update_downloads[" . $id . "][maxcount]' size='6' value='" . $downloads->products[$mm]['maxcount'] . "' onChange=\"updateDownloads('download_count', '" . $id . "', '" . $orders_products_id . "', this.value)\">";


     echo ' </nobr>' . "\n";
     echo '<br></small>';
     $downloads_count++;
     } //end  for ($mm=0; $mm<sizeof($download_query); $mm++) {
    }
   } //end download
  } //end if (sizeof($order->products[$i]['attributes']) > 0) {
?>
                </td>

      <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $orders_products_id . "][model]"; ?>" size="12" onChange="updateProductsField('update', '<?php echo $orders_products_id; ?>', 'products_model', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['model']; ?>"></td>

      <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $orders_products_id . "][tax]"; ?>" size="5" style="text-align:center;" onKeyUp="updatePrices('tax', '<?php echo $orders_products_id; ?>')" onChange="updateProductsField('reload1', '<?php echo $orders_products_id; ?>', 'products_tax', encodeURIComponent(this.value))" value="<?php echo tep_display_tax_value($order->products[$i]['tax']); ?>" id="<?php echo "update_products[" . $orders_products_id . "][tax]"; ?>">%</td>

        <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $orders_products_id . "][price]"; ?>" size="7" onKeyUp="updatePrices('price', '<?php echo $orders_products_id; ?>')" onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')" value="<?php echo number_format($order->products[$i]['price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $orders_products_id . "][price]"; ?>"></td>

      <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $orders_products_id . "][final_price]"; ?>" size="7" onKeyUp="updatePrices('final_price', '<?php echo $orders_products_id; ?>')" onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')" value="<?php echo number_format($order->products[$i]['final_price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $orders_products_id . "][final_price]"; ?>"></td>

      <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $orders_products_id . "][price_incl]"; ?>" size="7" value="<?php echo number_format(($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1)), 4, '.', ''); ?>" onKeyUp="updatePrices('price_incl', '<?php echo $orders_products_id; ?>')" onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')" id="<?php echo "update_products[" . $orders_products_id . "][price_incl]"; ?>"></td>

      <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $orders_products_id . "][total_excl]"; ?>" size="7" value="<?php echo number_format($order->products[$i]['final_price'] * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_excl', '<?php echo $orders_products_id; ?>')" onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')" id="<?php echo "update_products[" . $orders_products_id . "][total_excl]"; ?>"></td>

      <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $orders_products_id . "][total_incl]"; ?>" size="7" value="<?php echo number_format((($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1))) * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_incl', '<?php echo $orders_products_id; ?>')" onChange="updateProductsField('reload2', '<?php echo $orders_products_id; ?>')" id="<?php echo "update_products[" . $orders_products_id . "][total_incl]"; ?>"></td>

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
            
<!-- product_listing_eof //-->

    <div id="totalsBlock">
    <table width="100%">
      <tr><td>

            <table width="100%" cellpadding="0">
              <tr>
                <td valign="top" width="100%">
          <br>
            <div>
            <?php echo tep_draw_button(TEXT_ADD_NEW_PRODUCT, 'plus', tep_href_link('edit_orders_add_product.php',
            'oID=' . $_GET['oID'] . '&step=1'
            )); ?>
            <a href="<?php echo tep_href_link('edit_orders_add_product.php', 'oID=' . $_GET['oID'] . '&step=1'); ?>" target="addProducts" onClick="openWindow('<?php echo tep_href_link('edit_orders_add_product.php', 'oID=' . $_GET['oID'] . '&step=1'); ?>','addProducts');return false"><?php echo tep_image_button('button_add_article.gif', TEXT_ADD_NEW_PRODUCT); ?>
            </a><input type="hidden" name="subaction" value="">
            </div>
          <br>
          </td>

        <!-- order_totals bof //-->
                <td align="right" rowspan="2" valign="top" nowrap class="dataTableRow" style="border: 1px solid #C9C9C9;">
                  <table>
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent" width="15" nowrap> </td>
                      <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_OT_TOTALS; ?></td>
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
      echo '                  <tr class="' . $rowStyle . '">' . "\n";
      if ($order->totals[$i]['class'] != 'ot_total') {
        echo '                    <td class="dataTableContent" valign="middle" height="15">
    <script language="JavaScript" type="text/javascript">
    <!--
    document.write("<span id=\"update_totals['.$i.']\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');\"><img src=\"order_editor/images/plus.gif\" border=\"0\" alt=\"' . IMAGE_ADD_NEW_OT . '\" title=\"' . IMAGE_ADD_NEW_OT . '\"></a></span>");
    //-->
        </script></td>' . "\n";
      } else {
        echo '                    <td class="dataTableContent" valign="middle">&nbsp;</td>' . "\n";
      }

      echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" value="' . trim($order->totals[$i]['title']) . '" readonly="readonly"></td>' . "\n";

      if ($order->info['currency'] != DEFAULT_CURRENCY) 
        echo '                    <td class="dataTableContent">&nbsp;</td>' . "\n";
      
      echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '<input name="update_totals['.$i.'][value]" type="hidden" value="' . number_format($order->totals[$i]['value'], 2, '.', '') . '"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"></td>' . "\n" .
           '                  </tr>' . "\n";
    } else {
      if ($i % 2) {
        echo '                        <script language="JavaScript" type="text/javascript">
    <!--
    document.write("<tr class=\"' . $rowStyle . '\" id=\"update_totals['.$i.']\" style=\"visibility: hidden; display: none;\"><td class=\"dataTableContent\" valign=\"middle\" height=\"15\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i).']\', \'hidden\', \'update_totals['.($i-1).']\');\"><img src=\"order_editor/images/minus.gif\" border=\"0\" alt=\"' . IMAGE_REMOVE_NEW_OT . '\" title=\"' . IMAGE_REMOVE_NEW_OT . '\"></a></td>");
       //-->
        </script>

       <noscript><tr class="' . $rowStyle . '" id="update_totals['.$i.']" >' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15"></td></noscript>' . "\n";
      } else {
        echo '                  <tr class="' . $rowStyle . '">' . "\n" .
             '                    <td class="dataTableContent" valign="middle" height="15">
      <script language="JavaScript" type="text/javascript">
    <!--
    document.write("<span id=\"update_totals['.$i.']\"><a href=\"javascript:setCustomOTVisibility(\'update_totals['.($i+1).']\', \'visible\', \'update_totals['.$i.']\');\"><img src=\"order_editor/images/plus.gif\" border=\"0\" alt=\"' . IMAGE_ADD_NEW_OT . '\" title=\"' . IMAGE_ADD_NEW_OT . '\"></a></span>");
    //-->
        </script></td>' . "\n";
      }

       if (ORDER_EDITOR_USE_AJAX == 'true') {
    echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '" onChange="obtainTotals()"></td>' . "\n" .
           '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format($order->totals[$i]['value'], 2, '.', '') . '" size="6" onChange="obtainTotals()"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
       } else {
    echo '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][title]" id="'.$id.'[title]" value="' . trim($order->totals[$i]['title']) . '"></td>' . "\n" .
           '                    <td align="right" class="dataTableContent"><input name="update_totals['.$i.'][value]" id="'.$id.'[value]" value="' . number_format(floatval($order->totals[$i]['value']), 2, '.', '') . '" size="6"><input name="update_totals['.$i.'][class]" type="hidden" value="' . $order->totals[$i]['class'] . '"><input name="update_totals['.$i.'][id]" type="hidden" value="' . $shipping_module_id . '" id="' . $id . '[id]"></td>' . "\n";
       }

      if ($order->info['currency'] != DEFAULT_CURRENCY) echo '                    <td align="right" class="dataTableContent" nowrap>' . $order->totals[$i]['text'] . '</td>' . "\n";
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
                <table width="550" style="border: 1px solid #C9C9C9;">
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
        echo '                  <tr class="' . $rowClass . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this, \'' . $rowClass . '\')" onClick="selectRowEffect(this, ' . $r . '); setShipping(' . $r . ');">' .
             '                    <td class="dataTableContent" valign="top" align="left">
       <script language="JavaScript" type="text/javascript">
                   <!--
                    document.write("<input type=\"radio\" name=\"shipping\" id=\"shipping_radio_' . $r . '\" value=\"' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'].'\">");
                 //-->
                  </script>
       <input type="hidden" id="update_shipping[' . $r . '][title]" name="update_shipping[' . $r . '][title]" value="'.$shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'].'):">' . "\n" .
       '      <input type="hidden" id="update_shipping[' . $r . '][value]" name="update_shipping[' . $r . '][value]" value="'.tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']).'">' . "\n" .
       '      <input type="hidden" id="update_shipping[' . $r . '][id]" name="update_shipping[' . $r . '][id]" value="' . $shipping_quotes[$i]['id'] . '_' . $shipping_quotes[$i]['methods'][$j]['id'] . '">' . "\n" .
             '      <td class="dataTableContent" valign="top">' . $shipping_quotes[$i]['module'] . ' (' . $shipping_quotes[$i]['methods'][$j]['title'] . '):</td>' . "\n" .
             '      <td class="dataTableContent" align="right">' . $currencies->format(tep_add_tax($shipping_quotes[$i]['methods'][$j]['cost'], $shipping_quotes[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
             '                  </tr>';
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
    </div>
    </div> <!-- this is end of the master div for the whole totals/shipping area -->

