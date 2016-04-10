<?php
// reload_totals.php begins
// comparar con reload_totals_COTEJAR CAMBIOS.php
// Parece que incluye parches
      $shipping = array();
      if (is_array($_POST['update_totals'])) {
        foreach($_POST['update_totals'] as $total_index => $total_details) {
          extract($total_details, EXTR_PREFIX_ALL, "ot");
          if ($ot_class == "ot_shipping") {
            $shipping['cost'] = $ot_value;
            $shipping['title'] = $ot_title;
            $shipping['id'] = $ot_id;
          } // end if ($ot_class == "ot_shipping")
        } //end foreach
      } //end if is_array

      if (tep_not_null($shipping['id'])) {
        tep_db_query("UPDATE " . TABLE_ORDERS . " SET shipping_module = '" . $shipping['id'] . "' WHERE orders_id = '" . $oID . "'");
      }

      $order = new manualOrder($oID);
      $order->adjust_zones();
      $cart = new manualCart();
      $cart->restore_contents($oID);
      $total_count = $cart->count_contents();
      $total_weight = $cart->show_weight();
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the shipping quotes- if we don't have shipping quotes shipping tax calculation can't happen
//////////////////////////////////////////////////////////////////////////////////////////////////
      $shipping_modules = new shipping;
      $shipping_quotes = $shipping_modules->quote();

      if (DISPLAY_PRICE_WITH_TAX == 'true') {//extract the base shipping cost or the ot_shipping module will add tax to it again
        $module = substr($GLOBALS['shipping']['id'], 0, strpos($GLOBALS['shipping']['id'], '_'));
        $tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
        $order->info['total'] -= ( $order->info['shipping_cost'] - ($order->info['shipping_cost'] / (1 + ($tax /100))) );
        $order->info['shipping_cost'] = ($order->info['shipping_cost'] / (1 + ($tax /100)));
      }

      //this is where we call the order total modules
      require( 'order_editor/order_total.php');
      $order_total_modules = new order_total();
      $order_totals = $order_total_modules->process();
      $current_ot_totals_array = array();
      $current_ot_titles_array = array();
      $written_ot_totals_array = array();
      $written_ot_titles_array = array();
        //how many weird arrays can I make today?
      $current_ot_totals_query = tep_db_query("select class, title from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $oID . "' order by sort_order");
      while ($current_ot_totals = tep_db_fetch_array($current_ot_totals_query)) {
        $current_ot_totals_array[] = $current_ot_totals['class'];
        $current_ot_titles_array[] = $current_ot_totals['title'];
      }

      tep_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . $oID . "'");

      $j=1; //giving something a sort order of 0 ain't my bag baby
      $new_order_totals = array();

      if (is_array($_POST['update_totals'])) { //1
        foreach($_POST['update_totals'] as $total_index => $total_details) { //2
          extract($total_details, EXTR_PREFIX_ALL, "ot");
          if (!strstr($ot_class, 'ot_custom')) { //3
            for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //4
              if ($order_totals[$i]['code'] == 'ot_tax') { //5
                $new_ot_total = ((in_array($order_totals[$i]['title'], $current_ot_titles_array)) ? false : true);
              } else { //within 5
                $new_ot_total = ((in_array($order_totals[$i]['code'], $current_ot_totals_array)) ? false : true);
              }  //end 5 if ($order_totals[$i]['code'] == 'ot_tax')

              if ( ( ($order_totals[$i]['code'] == 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) && ($order_totals[$i]['title'] == $ot_title) ) || ( ($order_totals[$i]['code'] != 'ot_tax') && ($order_totals[$i]['code'] == $ot_class) ) ) { //6
              //only good for components that show up in the $order_totals array
                if ($ot_title != '') { //7
                  $new_order_totals[] = array('title' => $ot_title,
                                              'text' => (($ot_class != 'ot_total') ? $order_totals[$i]['text'] : '<b>' . $currencies->format($order->info['total'], true, $order->info['currency'], $order->info['currency_value']) . '</b>'),
                                              'value' => (($order_totals[$i]['code'] != 'ot_total') ? $order_totals[$i]['value'] : $order->info['total']),
                                              'code' => $order_totals[$i]['code'],
                                              'sort_order' => $j);
                  $written_ot_totals_array[] = $ot_class;
                  $written_ot_titles_array[] = $ot_title;
                  $j++;
                } else { //within 7
                  $order->info['total'] += ($ot_value*(-1));
                  $written_ot_totals_array[] = $ot_class;
                  $written_ot_titles_array[] = $ot_title;
                } //end 7

              } elseif ( ($new_ot_total) && (!in_array($order_totals[$i]['title'], $current_ot_titles_array)) ) { //within 6
                $new_order_totals[] = array('title' => $order_totals[$i]['title'],
                                            'text' => $order_totals[$i]['text'],
                                            'value' => $order_totals[$i]['value'],
                                            'code' => $order_totals[$i]['code'],
                                            'sort_order' => $j);
                $current_ot_totals_array[] = $order_totals[$i]['code'];
                $current_ot_titles_array[] = $order_totals[$i]['title'];
                $written_ot_totals_array[] = $ot_class;
                $written_ot_titles_array[] = $ot_title;
                $j++;
                //echo $order_totals[$i]['code'] . "<br>"; for debugging- use of this results in errors
              } elseif ($new_ot_total) { //also within 6
                $order->info['total'] += ($order_totals[$i]['value']*(-1));
                $current_ot_totals_array[] = $order_totals[$i]['code'];
                $written_ot_totals_array[] = $ot_class;
                $written_ot_titles_array[] = $ot_title;
              }//end 6
            }//end 4
          } elseif ( (tep_not_null($ot_value)) && (tep_not_null($ot_title)) ) { // this modifies if (!strstr($ot_class, 'ot_custom')) { //3
            $new_order_totals[] = array('title' => $ot_title,
                                        'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                                        'value' => $ot_value,
                                        'code' => 'ot_custom_' . $j,
                                        'sort_order' => $j);
            $order->info['total'] += $ot_value;

            $written_ot_totals_array[] = $ot_class;
            $written_ot_titles_array[] = $ot_title;
            $j++;
          } //end 3

          //save ot_skippy from certain annihilation
          if ( (!in_array($ot_class, $written_ot_totals_array)) && (!in_array($ot_title, $written_ot_titles_array)) && (tep_not_null($ot_value)) && (tep_not_null($ot_title)) && ($ot_class != 'ot_tax') && ($ot_class != 'ot_loworderfee') ) { //7
          //this is supposed to catch the oddball components that don't show up in $order_totals
            $new_order_totals[] = array('title' => $ot_title,
                                        'text' => $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']),
                                        'value' => $ot_value,
                                        'code' => $ot_class,
                                        'sort_order' => $j);
            //$current_ot_totals_array[] = $order_totals[$i]['code'];
            //$current_ot_titles_array[] = $order_totals[$i]['title'];
            $written_ot_totals_array[] = $ot_class;
            $written_ot_titles_array[] = $ot_title;
            $j++;

          } //end 7
        } //end 2
      } else {//within 1
        // $_POST['update_totals'] is not an array => write in all order total components that have been generated by the sundry modules
        for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { //8
          $new_order_totals[] = array('title' => $order_totals[$i]['title'],
                                      'text' => $order_totals[$i]['text'],
                                      'value' => $order_totals[$i]['value'],
                                      'code' => $order_totals[$i]['code'],
                                      'sort_order' => $j);
          $j++;

        } //end 8

      } //end if (is_array($_POST['update_totals'])) { //1

      for ($i=0, $n=sizeof($new_order_totals); $i<$n; $i++) {
        $sql_data_array = array('orders_id' => $oID,
                                'title' => oe_iconv($new_order_totals[$i]['title']),
                                'text' => $new_order_totals[$i]['text'],
                                'value' => $new_order_totals[$i]['value'],
                                'class' => $new_order_totals[$i]['code'],
                                'sort_order' => $new_order_totals[$i]['sort_order']);
        tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
      }

// reload_totals.php end