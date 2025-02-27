<?php

$updates = array(
  '0001-add_more_device_categories_to_mac_registry',
  '0002-add_userPass_NTPass_to_user_locks',
  '0003-add_user_default_password',
  '0004-add_ip_to_attribute_change_log',
  '0005-change_user_default_password_to_uid',
  '0006-add_message_tables',
  '0007-add_password_reset_tokens_table',
  '0008-change_user_lock_to_uid',
  '0009-add_staff_to_mac_registry_labs_cat',
  '0010-increase_password_token_lifespan_in_message',
  '0011-add_cybercorp_to_mac_registry_iot_cat',
  '0012-encrypt_lock_and_default',
  '0013-add_token_purpose_and_log',
  '0014-add_printer_to_mac_registry_iot_cat',
);
$results = array();

foreach ( $updates as $file ) {
  if ( is_readable( $file .".php" ) ) {
    $result = include( $file .".php" );
    if ( strlen($result) > 1 ) { $results[] = $result; }
  }
}

foreach ( $results as $msg ) {
  print $msg ."\n";
}

?>
