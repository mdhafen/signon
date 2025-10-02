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
<?php if ( $error == 'PASS_TOO_COMMON' ) { ?>
<p><span class="big">Password compromised, you can not use this password.</span><br>  This password has been seen <?= $data['error_times'] ?? "[Error]" ?> times before.  <span class="small">This password has previously appeared in a data breach and should never be used.  If you've ever used it anywhere before, you should change it as soon as possible.</span></p>
<?php } ?>
<?php
}
?>
</div>
<?php include( 'doc-close.php' ); ?>
