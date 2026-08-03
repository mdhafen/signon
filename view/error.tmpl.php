<?php include( 'doc-open.php' ); ?>
<title>Staff Login Management - Error</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>

<div class="container">
<h1>An error occured!</h1>

<?php
foreach ( $data['errors'] as $error ) {
?>
<div class='important'><?= htmlspecialchars($error,ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?></div>
<?php if ( is_array($error) ) { $error_info = $error['info']; $error = $error['flag']; } ?>

<?php if ( $error == 'PASS_TOO_COMMON' ) { ?>
<p><span class="big">Password has been compromised before, you can not use this password.</span><br>  This password has been seen <?= !empty($error_info) ? $error_info .' times' : "" ?> before.  <span class="small">This password has previously appeared in a data breach and should never be used.  If you've ever used it anywhere before, you should change it as soon as possible.</span></p>
<?php } ?>

<?php if ( $error == 'INVALID_TOKEN' ) { ?>
<p>This password reset link is invalid or expired.</p>
<?php } ?>

<?php if ( $error == 'REGISTERING_SERVICE_ACCOUNT' ) { ?>
<p>This is not a user account.</p>
<?php } ?>

<?php if ( $error == 'LDAP_CREATE' ) { ?>
<p>Couldn't create SignOn account for <?= $error_info ?></p>
<?php } ?>

<?php if ( $error == 'USER_LOCKED' ) { ?>
<p>This account is locked</p>
<?php } ?>

<?php if ( $error == 'USER_NOT_FOUND' ) { ?>
<p>Couldn't find the SignOn account for this user</p>
<?php } ?>

<?php if ( $error == 'WRONG_EMAIL_DOMAIN' ) { ?>
<p>This is not the right Google account.  How did you even get here?</p>
<?php } ?>

<?php if ( $error == 'USER_NOT_SIGNED_IN' ) { ?>
<p>You must sign in to your Google account.</p>
<?php } ?>

<?php
}
?>
</div>
<?php include( 'doc-close.php' ); ?>
