<?php
// $Id: uc_credomatic.pages.inc,v 1.2 2011/02/03 17:49:12 longwave Exp $

/**
 * Credomatic CPI callback page.
 */
function uc_credomatic_callback() {
  // If order is still in checkout, the hash matches, and the transaction was approved, log the payment.
  $order = uc_order_load($_POST['OrderId']);
  if ($order && uc_order_status_data($order->order_status, 'state') != 'in_checkout' && uc_credomatic_check_hash() && $_POST['CpiResultsCode'] == 0) {
    uc_payment_enter($order->order_id, 'credomatic', $_POST['PurchaseAmount'] / 100, 0, NULL, t('Paid via Credomatic CPI.'));
  }

  exit;
}

/**
 * Credomatic completion page.
 */
function uc_credomatic_complete() {
  // Check order ID and ensure order is not already complete.
  $order = uc_order_load($_POST['OrderId']);
  if ($order === FALSE || uc_order_status_data($order->order_status, 'state') != 'in_checkout') {
    return t('An error has occurred during payment.  Please contact us to ensure your order has submitted.');
  }

  // Check hash to ensure we have valid results.
  if (!uc_credomatic_check_hash()) {
    uc_order_comment_save($order->order_id, 0, t('Attempted completion with incorrect OrderHash.'), 'admin');
    return MENU_ACCESS_DENIED;
  }

  // Process result code.
  $code = $_POST['CpiResultsCode'];
  $message = _uc_credomatic_result_code($code);

  switch ($code) {
    case 0: // Transaction approved.
      $_SESSION['do_complete'] = TRUE;
      drupal_goto('cart/checkout/complete');
      break;

    case 1: // Cancelled by user.
      uc_order_comment_save($order->order_id, 0, 'Credomatic CPI: ' . $message, 'admin');
      drupal_goto('cart/checkout/review');
      break;

    case 9: // Transaction in FraudShield review state.
      drupal_set_message(t('Your order will be processed as soon as your payment clears.'));
      uc_order_comment_save($order->order_id, 0, t('Order is awaiting FraudShield review at Credomatic.'), 'admin');
      $_SESSION['do_complete'] = TRUE;
      drupal_goto('cart/checkout/complete');
      break;

    default:
      watchdog('uc_credomatic', 'Credomatic transaction error @code: !message', array('@code' => $code, '!message' => $message), WATCHDOG_ERROR, l(t('view order'), 'admin/store/orders/'. $order->order_id));
      uc_order_comment_save($order->order_id, 0, 'Credomatic CPI: ' . $message, 'admin');
      drupal_set_message(t('We were unable to process your payment. Please verify your details and try again. If the problem persists, contact us to complete your order.'), 'error');
      drupal_goto('cart/checkout/review');
  }
}

/**
 * Check that the POSTed data matches the secure hash.
 */
function uc_credomatic_check_hash() {
  $data = array($_POST['CpiResultsCode'], $_POST['PurchaseDate'], $_POST['OrderId'], $_POST['PurchaseAmount'], $_POST['PurchaseCurrency'], $_POST['ShopperEmail'], $_POST['StorefrontId']);
  return $_POST['OrderHash'] == uc_credomatic_generate_hash($data, variable_get('uc_credomatic_cpihashkey', ''));
}

function _uc_credomatic_result_code($code) {
  switch ($code) {
    case 0: return t('The transaction was approved.');
    case 1: return t('The user cancelled the transaction.');
    case 2: return t('The processor declined the transaction for an unknown reason.');
    case 3: return t('The transaction was declined because of a problem with the card. For example, an invalid card number or expiration date was specified.');
    case 4: return t('The processor did not return a response.');
    case 5: return t('The amount specified in the transaction was either too high or too low.');
    case 6: return t('The specified currency is not supported by either the processor or the card.');
    case 7: return t('The order is invalid because the order ID is a duplicate.');
    case 8: return t('The transaction was rejected by FraudShield.');
    case 9: return t('The transaction was placed in Review state by FraudShield.');
    case 10: return t('The transaction failed because of invalid input data.');
    case 11: return t('The transaction failed because the CPI was configured incorrectly.');
    case 12: return t('The transaction failed because the Storefront was configured incorrectly.');
    case 13: return t('The connection timed out.');
    case 14: return t("The transaction failed because the cardholder's browser refused a cookie.");
    case 15: return t("The customer's browser does not support 128-bit encryption.");
    case 16: return t('The CPI cannot communicate with the Secure ePayment engine.');
    default: return t('An unknown error occured.');
  }
}
