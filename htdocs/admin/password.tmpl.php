<?php include($data['_config']['base_dir'] .'/htdocs/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Set Password</title>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<h2>Set WCSDaccess and Google Password</h2>

<?php if ( ! empty($data['success']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Success!
 <?php if ( strcasecmp($data['object_dn'],$data['_session']['userid']) == 0 ) { ?>
<a class="btn" href="<?= $data['_config']['base_url'] ?>?_logout=1">Logout</a>
 <?php } else { ?>
<a class="btn" href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Back</a>
 <?php } ?>
</div>
<?php } else if ( ! empty($data['error']) ) { ?>
<div class="alert alert-danger" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<?= $data['error'] ?>
</div>
<?php } else { ?>
<div class="panel panel-default panel-body">
<div class="container-fluid">
<form method="post" action="password.php" class="form-horizontal">

<div class="row form-group">
<label for="password" class="col-sm-4 control-label">Password</label>
<div class="col-sm-8"><input type="password" id="password" name="password" class="form-control"></div>
</div>

<div class="row form-group">
<label for="confirm" class="col-sm-4 control-label">Repeat Password</label>
<div class="col-sm-8"><input type="password" id="confirm" name="confirm" class="form-control"></div>
</div>

<div class="row form-group">
<input type="submit" value="Set" class="btn">
<input type="hidden" name="dn" value="<?= htmlspecialchars($data['object_dn']) ?>">
<a class="btn" href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Cancel</a>
</div>
</div>
</form>
<?php } ?>

</div>
</div>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
