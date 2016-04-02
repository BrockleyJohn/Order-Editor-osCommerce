<?php
/*
  $Id: order_total.php,v 1.0 200/05/13 00:04:53 djmonkey1 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
  
  order_total.php is a clone of the original order_total.php class file from the catalog side
  if you have modified the file catalog/includes/classes/order_total.php in any way
  you will have to modify this file as well in order for your order total modules to behave the same via Order Editor
  
*/

  class order_total {
    var $modules;

// class constructor
    function order_total() {
      global $language;

      if (defined('MODULE_ORDER_TOTAL_INSTALLED') && tep_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
        $this->modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);

        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $value);
          include(DIR_FS_CATALOG_MODULES . 'order_total/' . $value);
		  
          $class = substr($value, 0, strrpos($value, '.'));
          $GLOBALS[$class] = new $class;
        }
      }
    }

    function process() {
      $order_total_array = array();
      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $GLOBALS[$class]->process();

            for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
              if (tep_not_null($GLOBALS[$class]->output[$i]['title']) && tep_not_null($GLOBALS[$class]->output[$i]['text'])) {
                $order_total_array[] = array('code' => $GLOBALS[$class]->code,
                                             'title' => $GLOBALS[$class]->output[$i]['title'],
                                             'text' => $GLOBALS[$class]->output[$i]['text'],
                                             'value' => $GLOBALS[$class]->output[$i]['value'],
                                             'sort_order' => $GLOBALS[$class]->sort_order);
              }
            }
          }
        }
      }

      return $order_total_array;
    }

    function output() {
      $output_string = '';
      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $size = sizeof($GLOBALS[$class]->output);
            for ($i=0; $i<$size; $i++) {
              $output_string .= '              <tr>' . "\n" .
                                '                <td align="right" class="main">' . $GLOBALS[$class]->output[$i]['title'] . '</td>' . "\n" .
                                '                <td align="right" class="main">' . $GLOBALS[$class]->output[$i]['text'] . '</td>' . "\n" .
                                '              </tr>';
            }
          }
        }
      }

      return $output_string;
    }
// RusNN modify to all total modules work - BOF
// It should be noted that the use of other discounts, not only Daily Specials, 
// they can also be removed! This mechanism would require revision
    function is_module_installed($module_class) {
      foreach ($this->modules as $value) {
        if (strpos($value, $module_class) !== false) return true;
      }
      return false;
    }

    function prepare_order_total_values() {
      global $order, $cart;

      // Most modules requires
      $GLOBALS['customer_id'] = (int)$order->customer['id'];

      // Enable ot_easy_discount module
      if ($this->is_module_installed('ot_easy_discount') && file_exists(DIR_WS_CLASSES . 'easy_discount.php')) {
        require_once(DIR_WS_CLASSES . 'easy_discount.php');
        $easy_discount = new easy_discount();

        $GLOBALS['easy_discount'] = &$easy_discount;

        require_once(DIR_WS_FUNCTIONS.'daily_specials_ot.php');

        $dsClear = true;

        if (DAILY_SPECIALS_ENABLE == 'true') {
           $ds_query = tep_db_query("select * from " . TABLE_DAILY_SPECIALS . " where date_start <= current_date() and date_stop >= current_date() and ( language_id = 99 or language_id = '" . (int)$languages_id . "')");

           if (tep_db_num_rows($ds_query) > 0)
           {
              $amt = 0;
              while ($ds = tep_db_fetch_array($ds_query))
              {
                $comparison = CheckConditions($ds, $cart, $amt);
                switch ($ds['condition1'])
                {
                   case TEXT_COUPON_CONDITION_CART_TOTAL:
                      if ($comparison)
                      {
                          $easy_discount->set('DS_CTTL',$ds['special_name'], $amt);
                          $dsClear = false;
                      } else {
                          $easy_discount->clear('DS_CTTL'.$ds['special_name']);
                      }
                      break;

                   case TEXT_COUPON_CONDITION_CART_QTY:
                      if ($comparison)
                      {
                          $easy_discount->set('DS_CQTY'.$ds['special_name'],$ds['special_name'],$amt);
                          $dsClear = false;
                      } else {
                          $easy_discount->clear('DS_CQTY'.$ds['special_name']);
                      }
                      break;
                   default:  //just displaying a banner //echo 'ERROR: No definition found -> '. $ds['condition1'];
                }
              }
           }
        }

        if ($dsClear) {
          if ($easy_discount->count() > 0)
          {
              $easy_discount->clear('DS_CTTL', true);
              $easy_discount->clear('DS_CQTY', true);
          }
        }
      } // ot_easy_discount
    }
// RusNN modify to all total modules work - EOF
	}
?>