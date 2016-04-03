<?php
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

   // UPDATE STATUS HISTORY & SEND EMAIL TO CUSTOMER IF NECESSARY #####

    $check_status_query = tep_db_query("
                        SELECT customers_name, customers_email_address, orders_status, date_purchased
                        FROM " . TABLE_ORDERS . "
              WHERE orders_id = '" . $oID . "'");

    $check_status = tep_db_fetch_array($check_status_query);

  if (($check_status['orders_status'] != $_GET['status']) || (tep_not_null($_GET['comments']))) {

        tep_db_query("UPDATE " . TABLE_ORDERS . " SET
            orders_status = '" . tep_db_input($_GET['status']) . "',
                      last_modified = now()
                      WHERE orders_id = '" . $oID . "'");

     // Notify Customer ?
      $customer_notified = '0';
      if (isset($_GET['notify']) && ($_GET['notify'] == 'true')) {
        $notify_comments = '';
        if (isset($_GET['notify_comments']) && ($_GET['notify_comments'] == 'true')) {
         $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, oe_iconv($_GET['comments'])) . "\n\n";
        }
        $email = STORE_NAME . "\n" .
                 EMAIL_SEPARATOR . "\n" .
             EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" .
  EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" .
             EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" .
             sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$_GET['status']]) . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE2);

        tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, $email, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

        $customer_notified = '1';
      }

      tep_db_query("INSERT into " . TABLE_ORDERS_STATUS_HISTORY . "
      (orders_id, orders_status_id, date_added, customer_notified, comments)
      values ('" . tep_db_input($oID) . "',
        '" . tep_db_input($_GET['status']) . "',
        now(),
        " . tep_db_input($customer_notified) . ",
        '" . oe_iconv($_GET['comments'])  . "')");
      }

include ("templates/commentsBlock.php");
?>
