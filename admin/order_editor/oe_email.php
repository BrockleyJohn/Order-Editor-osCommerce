<?php
// Case Email:
global $order, $oID;

        $oID = tep_db_prepare_input($_GET['oID']);
        $order = new manualOrder($oID);

// bof order editor 5 0 9
        $order_totals_table_beginn = '<table border="0" cellpadding="5" cellspacing="0">';
        $order_totals_zelle_beginn = '<tr><td width="280" style="font-size: 12px">';
        $order_totals_zelle_mitte = '</td><td style="font-size: 12px" align="right">';
        $order_totals_zelle_end = '</td></tr>';
        $order_totals_table_end = '</table>';


        // initialized for the email confirmation
        if (EMAIL_USE_HTML == 'true'){
        $products_ordered = $order_totals_table_beginn;
        } else{
        $products_ordered = '';
        }

    $subtotal = 0;
    $total_tax = 0;

// eof order editor 5 0 9

        for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
        //loop all the products in the order
        $products_ordered_attributes = '';
      if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
        for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        $products_ordered_attributes .= "\n\t" . $order->products[$i]['attributes'][$j]['option'] . ' ' . $order->products[$i]['attributes'][$j]['value'];
      }
    }

// bof order editor 5 0 9
//      $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . $products_model . ' = ' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . $products_ordered_attributes . "\n";
//      }
            $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
        $total_tax += tep_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
        $total_cost += $total_products_price;
      if (EMAIL_USE_HTML == 'true'){
          if ($order->products[$i]['model']) {
            $products_ordered .= $order_totals_zelle_beginn . $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $order_totals_zelle_mitte . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . $order_totals_zelle_end;
                } else {
            $products_ordered .= $order_totals_zelle_beginn . $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' = ' . $order_totals_zelle_mitte . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . $order_totals_zelle_end;
                }
      } else {
        if ($order->products[$i]['model']) {
          $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . "\n";
        } else {
          $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' = ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . "\n";
        }
      }
    }

        $Text_Billing_Adress= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
                                                     EMAIL_SEPARATOR . "\n" .
                                                                 $order->billing['name'] . "\n";
            if ($order->billing['company']) {
            $Text_Billing_Adress .= $order->billing['company'] . "\n";
        }
            $Text_Billing_Adress .= $order->billing['street_address'] . "\n";
            if ($order->billing['suburb']) {
                $Text_Billing_Adress .= $order->billing['suburb'] . "\n";
        }
            $Text_Billing_Adress .= $order->billing['city'] . "\n";
          if ($order->billing['state']) {
            $Text_Billing_Adress .= $order->billing['state'] . "\n";
            }
            $Text_Billing_Adress .= $order->billing['postcode'] . "\n" .
                                                            $order->billing['country'] . "\n\n";


            $Text_Delivery_Address = "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" .
                                                        EMAIL_SEPARATOR . "\n" .
                                                                            $order->delivery['name'] . "\n";
            if ($order->delivery['company']) {
            $Text_Delivery_Address .= $order->delivery['company'] . "\n";
        }
            $Text_Delivery_Address .= $order->delivery['street_address'] . "\n";
          if ($order->delivery['suburb']) {
            $Text_Delivery_Address .= $order->delivery['suburb'] . "\n";
          }
            $Text_Delivery_Address .= $order->delivery['city'] . "\n";
            if ($order->delivery['state']) {
            $Text_Delivery_Address .= $order->delivery['state'] . "\n";
        }
            $Text_Delivery_Address .= $order->delivery['postcode'] . "\n" . $order->delivery['country'] . "\n";

        $standaard_email = 'false' ;
        if ( FILENAME_EMAIL_ORDER_TEXT !== 'FILENAME_EMAIL_ORDER_TEXT' ){
            // only use if email order text is installed
        if (EMAIL_USE_HTML == 'true'){
                $products_ordered .= $order_totals_table_end;
            }
            if (EMAIL_USE_HTML == 'true'){
                $text_query = tep_db_query("SELECT * FROM eorder_text where eorder_text_id = '2' and language_id = '" . $languages_id . "'");
            } else{
                $text_query = tep_db_query("SELECT * FROM eorder_text where eorder_text_id = '1' and language_id = '" . $languages_id . "'");
            }

      $werte = tep_db_fetch_array($text_query);
      $text = $werte["eorder_text_one"];
            $text = preg_replace('/<-STORE_NAME->/', STORE_NAME, $text);
            $text = preg_replace('/<-insert_id->/', $oID, $text);
            $text = preg_replace('/<-INVOICE_URL->/', tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL', false), $text);
            $text = preg_replace('/<-DATE_ORDERED->/', tep_date_long( $order->info[ 'date_purchased' ] ), $text ) ;
            if ($order->info['comments']) {
                $text = preg_replace('/<-Customer_Comments->/', tep_db_output($order->info['comments']), $text);
        } else{
            $text = preg_replace('/<-Customer_Comments->/', '', $text);
        }
            $text = preg_replace('/<-Item_List->/', $products_ordered, $text);
            if (EMAIL_USE_HTML == 'true'){
            $list_total = $order_totals_table_beginn;
            for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
                    $list_total .= $order_totals_zelle_beginn . strip_tags($order->totals[$i]['title']) . $order_totals_zelle_mitte . strip_tags($order->totals[$i]['text']) . $order_totals_zelle_end;
                }
            $list_total .= $order_totals_table_end;
            } else{
            for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
                    $list_total .= strip_tags($order->totals[$i]['title']) . ' ' . strip_tags($order->totals[$i]['text']) . "\n";
                }
            }
            $text = preg_replace('/<-List_Total->/', $list_total, $text);
            if ($order->content_type != 'virtual') {
                $text = preg_replace('/<-DELIVERY_Adress->/', $Text_Delivery_Address , $text);
            }
            elseif($order->content_type == 'virtual') {
                    if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
                    $text = preg_replace('/<-DELIVERY_Adress->/', EMAIL_TEXT_DOWNLOAD_SHIPPING . "\n" . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL', false), $text);
                    } else{
                    $text = preg_replace('/<-DELIVERY_Adress->/', '', $text);
                    }
            } else{
            $text = preg_replace('/<-DELIVERY_Adress->/', '', $text);
            }
            $text = preg_replace('/<-BILL_Adress->/', $Text_Billing_Adress, $text);
            $text = preg_replace('/<-Payment_Modul_Text->/', $order->info['payment_method'], $text);
        $text = preg_replace('/<-Payment_Modul_Text_Footer->/', EMAIL_TEXT_FOOTER, $text);

            $text = preg_replace('/<-FIRMENANSCHRIFT->/', STORE_NAME_ADDRESS, $text);
            $text = preg_replace('/<-FINANZAMT->/', OWNER_BANK_FA, $text);
            $text = preg_replace('/<-STEUERNUMMER->/', OWNER_BANK_TAX_NUMBER, $text);
            $text = preg_replace('/<-USTID->/', OWNER_BANK_UST_NUMBER, $text);
            $text = preg_replace('/<-BANKNAME->/', OWNER_BANK_NAME, $text);
            $text = preg_replace('/<-KONTOINHABER->/', OWNER_BANK_ACCOUNT, $text);
            $text = preg_replace('/<-BLZ->/', STORE_OWNER_BLZ, $text);
            $text = preg_replace('/<-KONTONUMMER->/', OWNER_BANK, $text);
            $text = preg_replace('/<-SWIFT->/', OWNER_BANK_SWIFT, $text);
            $text = preg_replace('/<-IBAN->/', OWNER_BANK_IBAN, $text);

        $email_order = $text;
     } else {
        // the contribution Email HTML is not installed so we must use the standaard text email
        $standaard_email = 'true' ;
     }


    if ( $standaard_email == 'true' ) {
            //Build the standaard email
        $email_order =  STORE_NAME . "\n" .
                        EMAIL_SEPARATOR . "\n" .
                                            EMAIL_TEXT_ORDER_NUMBER . ' ' . (int)$oID . "\n" .
                                        EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$oID, 'SSL') . "\n" .
                          EMAIL_TEXT_DATE_MODIFIED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";

        $email_order .= EMAIL_TEXT_PRODUCTS . "\n" .
                            EMAIL_SEPARATOR . "\n" .
                          $products_ordered .
                      EMAIL_SEPARATOR . "\n";

        for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
        $email_order .= strip_tags($order->totals[$i]['title']) . ' ' . strip_tags($order->totals[$i]['text']) . "\n";
        }

        if ($order->content_type != 'virtual') {
            $email_order .= $Text_Delivery_Address   ;
        }

        $email_order .= $Text_Billing_Adress ;

        $email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" .
                          EMAIL_SEPARATOR . "\n";
        $email_order .= $order->info['payment_method'] . "\n\n";


        //  if ( ($order->info['payment_method'] == ORDER_EDITOR_SEND_INFO_PAYMENT_METHOD) && (EMAIL_TEXT_PAYMENT_INFO) ) {
        //     $email_order .= EMAIL_TEXT_PAYMENT_INFO . "\n\n";
        //   }
        //I'm not entirely sure what the purpose of this is so it is being shelved for now
    }
// eof order editor 5 0 9

        if (EMAIL_TEXT_FOOTER) {
            $email_order .= EMAIL_TEXT_FOOTER . "\n\n";
      }

    //code for plain text emails which changes the € sign to EUR, otherwise the email will show ? instead of €
    $email_order = str_replace("€","EUR",$email_order);
      $email_order = str_replace("&nbsp;"," ",$email_order);

      //code which replaces the <br> tags within EMAIL_TEXT_PAYMENT_INFO and EMAIL_TEXT_FOOTER with the proper \n
      $email_order = str_replace("<br>","\n",$email_order);

      // picture mode
      $email_order = tep_add_base_ref($email_order);

      //send the email to the customer
// bof order editor 5_0_8
//    tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      //tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
// bof added for pdfinvoice email attachment:
    if (FILENAME_PDF_INVOICE    !== 'FILENAME_PDF_INVOICE'    ) {
         if ( ORDER_EDITOR_ADD_PDF_INVOICE_EMAIL == 'true' ) {
        // All we do is set the order_id for pdfinvoice.php to pick up
        //$HTTP_GET_VARS['order_id'] = $insert_id;
        // set stream mode
        $stream = true;
        $oID= $_GET['oID'] ;
        $invoice_number = $_GET['oID'] ;
        $pdf_data = '' ;
        $pdf_data = include_once(FILENAME_PDF_INVOICE );
        $file_name = $_GET['oID'] .'.pdf' ;
        // add text to email informing customer a pdf invoice copy has been attached:
        $email_order .= 'PDF attached' ."\n\n";
        $file_name = $_GET['oID'] .'.pdf' ;
        // send email with pdf invoice attached. Check to make sure pdfinvoice.php returns some data, else send standard email
        // note $order object reinstantiated by inclusion of pdfinvoice.php hence customer['name']
        if (tep_not_null($pdf_data)) {
            tep_mail_string_attachment($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $pdf_data, $file_name);
        } else {
            tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
      } else {
        // send vanilla e-mail - if email attachment option is false
        tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }
    } else {
        // send vanilla e-mail - if email attachment option is false
        tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
    }

// eof added for pdfinvoice email attachment:

// eof order editor 5_0_8

   // send emails to other people as necessary
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
  }

         //do the dirty

        $messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');

        tep_redirect(tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action')) . 'action=edit'));
