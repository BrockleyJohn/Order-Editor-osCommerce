<?php
/*
  $Id: edit_orders.php v5.0.9 08/27/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License

  For Order Editor support or to post bug reports, feature requests, etc, please visit the Order Editor support thread:
  http://forums.oscommerce.com/index.php?showtopic=54032

  Eliminados addons superfluos 31/03/2016 JMC
  
*/

  require('includes/application_top.php');

    // check for database field shipping_module on table orders. Move later to a module
   if (!tep_db_num_rows(tep_db_query("SHOW COLUMNS FROM orders LIKE 'shipping_module'"))) {
    tep_db_query ("alter table orders add shipping_module varchar(255) NULL");
    // este campo será para saber si el pedido ha sido alterado manualmente
    // se incluirá un boton para volver a la versión original que comprobará ese campo
    // habrá que crear tablas nuevas para el backup: oe_orders, oe_orders_products...
   }
   
  // include the appropriate functions & classes
  include('order_editor/functions.php');
  include('order_editor/cart.php');
  include('order_editor/order.php');
  include('order_editor/shipping.php');
//  include('order_editor/http_client.php');
  include(DIR_WS_LANGUAGES . $language. '/' . 'edit_orders.php');

  // Include currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

 //orders status
  $orders_statuses = array();
  $orders_status_array = array();
  $orders_status_query = tep_db_query("SELECT orders_status_id, orders_status_name
                                       FROM " . TABLE_ORDERS_STATUS . "
                                       WHERE language_id = '" . (int)$languages_id . "'");

  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                               'text' => $orders_status['orders_status_name']);

    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
  }

  $oID = (isset($_GET['oID']) ? tep_db_prepare_input($_GET['oID']) : '');
  
        if (!$oID) {
        $messageStack->add(ERROR_NO_ORDER_SELECTED, 'error');
          break;
          }
//        $oID = tep_db_prepare_input($_GET['oID']);
        $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . $oID . "'");
        $order_exists = true;
        if (!tep_db_num_rows($orders_query)) {
        $order_exists = false;
          $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
          break;
        }

        $order = new manualOrder($oID);
        $shippingKey = $order->adjust_totals($oID);
        $order->adjust_zones();

        $cart = new manualCart();
        $cart->restore_contents($oID);
        $total_count = $cart->count_contents();
        $total_weight = $cart->show_weight();

        // Get the shipping quotes
        $shipping_modules = new shipping;
        $shipping_quotes = $shipping_modules->quote();

  // currecies drop-down array
  $currency_query = tep_db_query("select distinct title, code from " . TABLE_CURRENCIES . " order by code ASC");
  $currency_array = array();
  while($currency = tep_db_fetch_array($currency_query)) {
    $currency_array[] = array('id' => $currency['code'],
                              'text' => $currency['code'] . ' - ' . $currency['title']);
  }
  require(DIR_WS_INCLUDES . 'template_top.php');
?>
 
<?php include('order_editor/css.php'); //because if you haven't got your css, what have you got? ?>

<script language="javascript" src="includes/general.js"></script>

<?php include('order_editor/javascript.php'); //because if you haven't got your javascript, what have you got? ?>


<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
    <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
    </table>
    </td>
<!-- body_text //-->
    <td width="100%" valign="top">
    
 <?php

     echo tep_draw_form('edit_order', basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=update_order');
 ?>
    <div id="header">
      <p id="headerTitle" class="pageHeading"><?php echo sprintf(HEADING_TITLE, $oID, tep_datetime_short($order->info['date_purchased'])); ?></p>
        <ul>
          <li><?php echo tep_draw_button(IMAGE_ORDERS, "document", tep_href_link( FILENAME_ORDERS , 'oID=' . $oID . '&action=edit'), null); ?></li>
          
          <li><?php echo tep_draw_button(IMAGE_ORDERS_INVOICE, 'document', tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $_GET['oID']), null, array('newwindow' => true)); ?></li>
          
          <li><?php echo tep_draw_button(IMAGE_ORDERS_PACKINGSLIP, 'document', tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $_GET['oID']), null, array('newwindow' => true)) ?></li>
          <li><?php echo tep_draw_button(IMAGE_BACK, 'triangle-1-w', tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));?></li>
        </ul>
      </div>
      <div id="ordersMessageStack">
        <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
      </div>
      <div  style="columns: 400px;">
      <!-- customer_info bof //-->
            <!-- customer_info bof //-->
<!-- probar float:left -->
        <div style="min-height:200px;float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
      <?php include ("order_editor/templates/customer_info.php");?>
        </div>
              <!-- customer_info_eof //-->

              <!-- shipping_address bof -->
        <div style="min-height:200px;float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
      <?php include ("order_editor/templates/shipping.php");?>
        </div>
              <!-- shipping_address_eof //-->
                <!-- billing_address bof //-->
        <div style="min-height:200px;float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
        <?php include ("order_editor/templates/billing.php");?>
        </div>
                <!-- billing_address eof //-->

                <!-- payment_method bof //-->
        <div style="float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
        <?php include ("order_editor/templates/payment_method.php");?>
        </div>
                <!-- contact_information bof //-->
        <div style="float:left;display: inline-block;vertical-align: top;margin: 0.5em;">
        <?php include ("order_editor/templates/contact_information.php");?>
        </div>
                <!-- contact_information eof //-->
        </div>
      </div>
    <div id="productsMessageStack">
      <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>
    <div width="100%" style="border: 1px solid #C9C9C9;">
      <a name="products"></a>
        <!-- product_listing bof //-->
        <table border="0" width="100%" cellspacing="0" cellpadding="2" id="productsTable">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent"><div align="center"><?php echo TABLE_HEADING_DELETE; ?></div></td>
            <td class="dataTableHeadingContent"><div align="center"><?php echo TABLE_HEADING_QUANTITY; ?></div></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_BASE_PRICE;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_BASE_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_PRICE_EXCL;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_UNIT_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_PRICE_INCL;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_UNIT_PRICE_TAXED;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_TOTAL_EXCL;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_TOTAL_PRICE;?></td>
            <td class="dataTableHeadingContent" align="center" title="<?php echo HINT_TOTAL_INCL;?>"><img src="images/icon_info.gif" border="0" width="13" height="13"> <?php echo TABLE_HEADING_TOTAL_PRICE_TAXED;?></td>
          </tr>
  <?php
  if (sizeof($order->products)) {
    for ($i=0; $i<sizeof($order->products); $i++) {
      $pID = $order->products[$i]['orders_products_id'];  ?>
          <tr class="dataTableRow">
            <td class="dataTableContent" valign="top"><div align="center"><input type="checkbox" name="<?php echo "update_products[" . $pID . "][delete]"; ?>" onClick="updateProductsField('delete', '<?php echo $pID; ?>', 'delete', this.checked, this)"></div></td>
            <td class="dataTableContent" valign="top"><div align="center"><input type="tel" name="<?php echo "update_products[" . $pID . "][qty]"; ?>" size="2" onKeyUp="updatePrices('qty', '<?php echo $pID; ?>')" onChange="updateProductsField('reload1', '<?php echo $pID; ?>', 'products_quantity', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['qty']; ?>" id="<?php echo "update_products[" . $pID . "][qty]"; ?>"></div></td>
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
            <td class="dataTableContent" valign="top"><input name="<?php echo "update_products[" . $pID . "][model]"; ?>" size="12" onChange="updateProductsField('update', '<?php echo $pID; ?>', 'products_model', encodeURIComponent(this.value))" value="<?php echo $order->products[$i]['model']; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][tax]"; ?>" size="5" onKeyUp="updatePrices('tax', '<?php echo $pID; ?>')" onChange="updateProductsField('reload1', '<?php echo $pID; ?>', 'products_tax', encodeURIComponent(this.value))" value="<?php echo tep_display_tax_value($order->products[$i]['tax']); ?>" id="<?php echo "update_products[" . $pID . "][tax]"; ?>">%</td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][price]"; ?>" size="7" onKeyUp="updatePrices('price', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" value="<?php echo @number_format($order->products[$i]['price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $pID . "][price]"; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][final_price]"; ?>" size="7" onKeyUp="updatePrices('final_price', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" value="<?php echo @number_format($order->products[$i]['final_price'], 4, '.', ''); ?>" id="<?php echo "update_products[" . $pID . "][final_price]"; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][price_incl]"; ?>" size="7" value="<?php echo number_format(($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1)), 4, '.', ''); ?>" onKeyUp="updatePrices('price_incl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][price_incl]"; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][total_excl]"; ?>" size="7" value="<?php echo number_format($order->products[$i]['final_price'] * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_excl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][total_excl]"; ?>"></td>
            <td class="dataTableContent" valign="top"><input type="tel" name="<?php echo "update_products[" . $pID . "][total_incl]"; ?>" size="7" value="<?php echo number_format((($order->products[$i]['final_price'] * (($order->products[$i]['tax']/100) + 1))) * $order->products[$i]['qty'], 4, '.', ''); ?>" onKeyUp="updatePrices('total_incl', '<?php echo $pID; ?>')" onChange="updateProductsField('reload2', '<?php echo $pID; ?>')" id="<?php echo "update_products[" . $pID . "][total_incl]"; ?>"></td>
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
        </table><!-- product_listing_eof //-->
        <div id="totalsBlock">
<?php include ("order_editor/templates/totalsBlock.php");?>
      </div>
    </div> <!-- this is end of the master div for the whole totals/shipping area -->

    <div id="historyMessageStack">
        <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>
    <div id="commentsBlock">
<?php include ("order_editor/templates/commentsBlock.php");?>

  </div>

      <div>
      <?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?>
      </div>
      <br>

<table style="border: 1px solid #C9C9C9;" cellspacing="0" cellpadding="2" class="dataTableRow">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_NEW_STATUS; ?></td>
    <td class="main" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
  </tr>
    <tr>
      <td>
          <table border="0" cellspacing="0" cellpadding="2">

        <tr>
          <td class="main"><b><?php echo ENTRY_STATUS; ?></b></td>
          <td class="main" align="right"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, $order->info['orders_status'], 'id="status"'); ?></td>
        </tr>
        <tr>
          <td class="main"><b><?php echo ENTRY_NOTIFY_CUSTOMER; ?></b></td>
          <td class="main" align="right"><?php echo oe_draw_checkbox_field('notify', '', false, '', 'id="notify"'); ?></td>
        </tr>
        <tr>
          <td class="main"><b><?php echo ENTRY_NOTIFY_COMMENTS; ?></b></td>
          <td class="main" align="right"><?php echo oe_draw_checkbox_field('notify_comments', '', false, '', 'id="notify_comments"'); ?></td>
        </tr>
     </table>
      </td>
    <td class="main" width="10">&nbsp;</td>
    <td class="main">
    <?php echo tep_draw_textarea_field('comments', 'soft', '40', '5', '', 'id="comments"'); ?>
    </td>
  </tr>



<tr>
  <td colspan="3" align="center">
  <?php echo tep_draw_button (oe_html_no_quote(AJAX_SUBMIT_COMMENT),"plus", null,'secondary', array('params' => ' onClick="javascript:getNewComment();"'));?>
  </td>
</tr>
  </table>

    <div>
      <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>

    <!-- End of Status Block -->

<?php
  echo '</form>';
?>
  <!-- body_text_eof //-->
      </td>
    </tr>
  </table>
  <!-- body_eof //-->
<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
  <script>
  $(function() {
    $( document ).tooltip();
  });
  </script>