<?php
    $order = new manualOrder($oID);

    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    //loop all the products in the order
       $products_ordered_attributes = '';
    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
    $products_ordered_attributes .= "\n\t" . $order->products[$i]['attributes'][$j]['option'] . ' ' . $order->products[$i]['attributes'][$j]['value'];
      }
    }

     $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . $products_model . ' = ' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . $products_ordered_attributes . "\n";
       }

       //Build the email
       $email_order = STORE_NAME . "\n" .
                      EMAIL_SEPARATOR . "\n" .
                      EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" .
                      EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" .
                      EMAIL_TEXT_DATE_MODIFIED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";

       $email_order .= EMAIL_TEXT_PRODUCTS . "\n" .
                       EMAIL_SEPARATOR . "\n" .
                       $products_ordered .
                       EMAIL_SEPARATOR . "\n";

       for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
       $email_order .= strip_tags($order->totals[$i]['title']) . ' ' . strip_tags($order->totals[$i]['text']) . "\n";
     }

     if ($order->content_type != 'virtual') {
       $email_order .= "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" .
                       EMAIL_SEPARATOR . "\n" .
                       $order->delivery['name'] . "\n";
       if ($order->delivery['company']) {
         $email_order .= $order->delivery['company'] . "\n";
       }
       $email_order .= $order->delivery['street_address'] . "\n";
         if ($order->delivery['suburb']) {
           $email_order .= $order->delivery['suburb'] . "\n";
         }
         $email_order .= $order->customer['city'] . "\n";
         if ($order->delivery['state']) {
           $email_order .= $order->delivery['state'] . "\n";
         }
         $email_order .= $order->customer['postcode'] . "\n" .
         $order->delivery['country'] . "\n";
       }

      $email_order .= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
                      EMAIL_SEPARATOR . "\n" .
            $order->billing['name'] . "\n";
            if ($order->billing['company']) {
                      $email_order .= $order->billing['company'] . "\n";
                      }
    $email_order .= $order->billing['street_address'] . "\n";
                    if ($order->billing['suburb']) {
                      $email_order .= $order->billing['suburb'] . "\n";
                      }
    $email_order .= $order->customer['city'] . "\n";
                    if ($order->billing['state']) {
                      $email_order .= $order->billing['state'] . "\n";
                      }
    $email_order .= $order->customer['postcode'] . "\n" .
            $order->billing['country'] . "\n\n";

      $email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" .
                      EMAIL_SEPARATOR . "\n";
      $email_order .= $order->info['payment_method'] . "\n\n";


      //  if ( ($order->info['payment_method'] == ORDER_EDITOR_SEND_INFO_PAYMENT_METHOD) && (EMAIL_TEXT_PAYMENT_INFO) ) {
          //     $email_order .= EMAIL_TEXT_PAYMENT_INFO . "\n\n";
           //   }
       //I'm not entirely sure what the purpose of this is so it is being shelved for now

      if (EMAIL_TEXT_FOOTER) {
        $email_order .= EMAIL_TEXT_FOOTER . "\n\n";
      }

      //code for plain text emails which changes the € sign to EUR, otherwise the email will show ? instead of €
      $email_order = str_replace("€","EUR",$email_order);
      $email_order = str_replace("&nbsp;"," ",$email_order);

      //code which replaces the <br> tags within EMAIL_TEXT_PAYMENT_INFO and EMAIL_TEXT_FOOTER with the proper \n
      $email_order = str_replace("<br>","\n",$email_order);

      //send the email to the customer
      tep_mail($order->customer['name'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

      // send emails to other people as necessary
      if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
        tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      }

  ?>

  <table>
    <tr>
      <td class="messageStackSuccess">
      <?php echo tep_image(DIR_WS_ICONS . 'success.gif', ICON_SUCCESS) . '&nbsp;' . sprintf(AJAX_SUCCESS_EMAIL_SENT, $order->customer['email_address']); ?>
    </td>
    </tr>
  </table>
