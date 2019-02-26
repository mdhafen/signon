<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
<div class="container">
<div class="mainpage">
<h2>Renew your WCSD Wifi Guest Account</h2>
<form action="renew.php" method="post" class="form-horizontal">
<?php if ( empty($data['token']) ) { ?>
<div class="row form-group">
    <div class="help-block">Please enter the token at the end of the web site address you recieved in an SMS</div>
    <input type="text" name="t" value="" class="form-control">
</div>
<?php } else { ?>
    <input type="hidden" name="t" value="<?= $data['token'] ?>">
<?php } ?>
<p>
    Please review our Acceptable Use and Technology Security policies.  You must agree to these policies to use our WiFi.  Clicking on the 'Agree' button below constitutes a digital signature.
</p>
<p><a target="_BLANK" href="<?= $data['_config']['base_url'] ?>get_aup.php">Acceptable Use Policy</a></p>
<!-- <p><a target="_BLANK" href="<?= $data['_config']['base_url'] ?>get_tsp.php">Technology Security Policy</a></p> -->

<div class="row form-group">
    <input class="btn btn-primary" type="submit" name="submit" value="Agree">
</div>

</form>
</div>
</div>

</div>
<?php include( 'doc-close.php' ); ?>
