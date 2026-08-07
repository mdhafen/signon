#!/usr/bin/env php
<?php
include_once('../lib/config.phpm');
include_once('../lib/data.phpm');
include_once('../inc/person.phpm');

if ( !empty($argv[1]) ) {
  $uid = $argv[1];
}

if ( !empty($uid) ) {
    $obj = array('uid'=>array($uid));
    $ad = new LDAP_Wrapper('AD');
    list($has_ad_pass,$has_ad_enable) = has_ad_account($obj,$ad);
    print "$has_ad_pass:$has_ad_enable\n";
}
?>
