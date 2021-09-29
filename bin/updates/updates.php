<?php

$updates = array(
  '0001-add_more_device_categories_to_mac_registry',
  '0002-add_userPass_NTPass_to_user_locks',
  '0003-add_user_default_password',
  '0004-add_ip_to_attribute_change_log',
  '0005-change_user_default_password_to_uid',
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
