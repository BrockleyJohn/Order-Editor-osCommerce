<?php
/*
  $Id: edit_orders.php v5.0.5 08/27/2007 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License http://www.gnu.org/licenses/

    Order Editor is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

  For Order Editor support or to post bug reports, feature requests, etc, please visit the Order Editor support thread:
  http://forums.oscommerce.com/index.php?showtopic=54032

  The original Order Editor contribution was written by Jonathan Hilgeman of SiteCreative.com

  Much of Order Editor 5.x is based on the order editing file found within the MOECTOE Suite Public Betas written by Josh DeChant

  Many, many people have contributed to Order Editor in many, many ways.  Thanks go to all- it is truly a community project.

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


  include('includes/classes/order.php');

  // Include currencies class
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

  <?php
  $action = (isset($_GET['action']) ? $_GET['action'] : 'edit');
  $oID = (isset($_GET['oID']) ? tep_db_prepare_input($_GET['oID']) : null);
  $order = new order($oID);

  if (isset($action)) {
    switch ($action) {
    // case 'update_order':
    //  Subaction-> case 'add_product':
    // case 'email':
    // case 'edit':
    
    ////
    // Update Order

    case 'update_order':
      require('order_editor/oe_update_order.php');
      
    break;
 // end case 'update_order':

    // 3. NEW ORDER EMAIL ###############################################################################################
    case 'email':
      require('order_editor/oe_email.php');
     break; // end case 'email'
    ////
    // Edit Order
      case 'edit':
              if (!$oID) {
//        $messageStack->add(ERROR_NO_ORDER_SELECTED, 'error');
          break;
          }

        require('order_editor/oe_edit.php');
        break; // end case 'edit':
    } // end switch $action
  }

?>

  <?php include('order_editor/css.php');
  
      //because if you haven't got your css, what have you got?
      ?>
  <?php include('order_editor/javascript.php');
      //because if you haven't got your javascript, what have you got?
      ?>
  <script>
  $(function() {
    $( document ).tooltip();
  });
  </script>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,300italic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="edit_orders.css">
 
<div id="barullo" style="width: 100%;">

<?php
   echo tep_draw_form('edit_order', 'edit_orders.php', tep_get_all_get_params(array('action')) . 'action=update_order');
 ?>
  <div id="header" class="totald">
<h2>aa</h2>
  </div>
  <div id="ordersMessageStack" class="total">
    <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
  </div>


      <div id="customer_info" class="infoblock">
<!-- customer_info bof //-->
<?php include ("order_editor/templates/t_cinfo.php");?>
<!-- customer_info_eof //-->
      </div>

      <div id="billing_address" class="infoblock">
<!-- billing_address bof -->
<?php  include ("order_editor/templates/t_billing.php");?>
<!-- billing_address eof //-->
      </div>
      
      <div id="shipping_address" class="infoblock">
<!-- shipping_address bof -->
<?php include ("order_editor/templates/t_shipping.php");?>
<!-- shipping_address_eof //-->
      </div>
      
      
<br style="clear: both;" />

  <div id="productsMessageStack"  class="total">
    <?php // echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>

      <div id="products" class="infoblock"><a name="products"></a>
<!-- product_listing bof -->
<?php  include ("order_editor/templates/t_product_listing.php");?>
<!-- product_listing eof //-->
      </div>

      
    <div id="historyMessageStack">
      <?php // echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
    </div>

    <div id="commentsBlock" class="infoblock">
<!-- comments bof -->
<?php // include ("order_editor/templates/t_comments.php");?>
<!-- comments eof //-->
  </div>

      <div>
    <?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?>
    </div>
    <br>
    <div id="commentsUpdateBlock" class="infoblock">
<!-- comments updater bof -->
<?php // include ("order_editor/templates/t_comments_update.php");?>
<!-- comments updater eof//-->
</div>
    <div>
    <?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
  </div>

  <!-- End of Status Block -->


</form>
</div>

  <!-- body_text_eof //-->

  <!-- body_eof //-->

  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
  <br>


  <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>