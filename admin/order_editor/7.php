<?php
            //check first to see if product should be deleted
            //update quantities first
            // adds stock
            if (STOCK_LIMITED == 'true'){
              tep_db_query("UPDATE " . TABLE_PRODUCTS . " SET
                            products_quantity = products_quantity + " . $quantity . ",
                            products_ordered = products_ordered - " . $quantity . "
                            WHERE products_id = '" . $order_products['products_id'] . "'");
// QT Pro Addon BOF 7
              if (ORDER_EDITOR_USE_QTPRO == 'true') {
                $attrib_q = tep_db_query("SELECT DISTINCT op.products_id, po.products_options_id, pov.products_options_values_id
                                          FROM products_options po, products_options_values pov, products_options_values_to_products_options po2pov, orders_products_attributes opa, orders_products op
                                          WHERE op.orders_id = '" . $oID . "'
                                          AND op.orders_products_id = '" . $pID . "'
                                          AND products_options_values_name = opa.products_options_values
                                          AND pov.products_options_values_id = po2pov.products_options_values_id
                                          AND po.products_options_id = po2pov.products_options_id
                                          AND products_options_name = opa.products_options");

                while($attrib_set = tep_db_fetch_array($attrib_q)) {
                  // corresponding to each option find the attribute ids ( opts and values id )
                  $products_stock_attributes[] = $attrib_set['products_options_id'].'-'.$attrib_set['products_options_values_id'];
                }

                sort($products_stock_attributes, SORT_NUMERIC); // Same sort as QT Pro stock
                $products_stock_attributes = implode($products_stock_attributes, ',');

                tep_db_query("UPDATE " . TABLE_PRODUCTS_STOCK . 
                             " SET products_stock_quantity = products_stock_quantity + " . $quantity . 
                             " WHERE products_id= '" . $order_products['products_id'] . "' and products_stock_attributes='".$products_stock_attributes."'");
              }
// QT Pro Addon EOF 7
            } else {
              tep_db_query ("UPDATE " . TABLE_PRODUCTS . " SET
                             products_ordered = products_ordered - " . $quantity . "
                             WHERE products_id = '" . $order_products['products_id'] . "'");
            } //end if (STOCK_LIMITED == 'true'){

            tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . "
                          WHERE orders_id = '" . $oID . "'
                          AND orders_products_id = '" . $pID . "'");

              tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                            WHERE orders_id = '" . $oID . "'
                            AND orders_products_id = '" . $pID . "'");

              tep_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . "
                            WHERE orders_id = '" . $oID . "'
                            AND orders_products_id = '" . $pID . "'");