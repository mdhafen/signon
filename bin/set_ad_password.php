#!/usr/bin/env php
<?php
include_once('../lib/config.phpm');
include_once('../lib/data.phpm');
include_once('../inc/person.phpm');

$uid = readline( "Username: " );

$tty = stream_isatty(STDIN);

if ( $tty ) { print "New password: "; }

/* possible shell to stty solution */
if ( $tty ) { shell_exec('/usr/bin/env stty -echo'); }
$pass = rtrim( fgets(STDIN, 4096), PHP_EOL );
if ( $tty ) { shell_exec('/usr/bin/env stty echo'); }


/* possible pure-php solution
$stdin = fopen('php://stdin','r');
stream_set_blocking($stdin,false);
stream_set_timeout($stdin,1);
$pass = stream_get_line($stdin,4096,PHP_EOL);
 */

//  AD ldap connection MUST be first or CACertFile option will not take effect
if ( !empty($uid) && !empty($pass) ) {
    $ad = new LDAP_Wrapper('AD');
    $result = set_ad_password($ad,$uid,$pass);
    if ( !empty($result) ) {
        print "Error! $result\n";
    }
}
else {
    print "Error! Missing username and/or password input\n";
}

?>
