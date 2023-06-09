<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
<div class="container">
<div class="mainpage">
<?php
  if ( !empty($data['error']) ) { ?>
<div class="alert alert-danger" role="alert">
There was an error! <?= $data['error'] ?>
</div>
<?php
  }
?>

<h2>Renew your WCSD Wifi Guest Account</h2>
<?php if ( !empty($data['recorded']) ) { ?>
<p>We have recorded your signature.  Thank you.</p>
<?php } else { ?>
<form action="renew.php" method="post" class="form-horizontal">
<?php if ( empty($data['token']) ) { ?>
<div class="row form-group">
    <div class="help-block">Please enter the token at the end of the web site address you recieved in the SMS</div>
    <input type="text" name="t" value="" class="form-control">
</div>
<?php } else { ?>
    <input type="hidden" name="t" value="<?= $data['token'] ?>">
<?php } ?>

<div class="row text-left">
<?php include( $data['_config']['base_dir'] .'/view/create-guest/guest-tos.php' ); ?>
</div>

<div class="row form-group">
    <input class="btn btn-primary" type="submit" name="submit" value="I accept this agreement">
</div>

</form>
<?php } ?>
</div>
</div>

</div>
<?php include( 'doc-close.php' ); ?>
