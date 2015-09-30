<?php include($data['_config']['base_dir'] .'/htdocs/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Register Lab Device</title>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<div class="main-page">

<h1>Register Lab Device</h1>

<?php if ( ! empty($data['success']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Success!
</div>
<?php } ?>

<div class="panel panel-default panel-body">
<div class="container-fluid">
<form method="post" action="register.php" class="form-horizontal">

<div class="row form-group">
<label for="client_mac" class="col-sm-4 control-label">M.A.C. Address</label>
<div class="col-sm-8"><input type="text" id="client_mac" name="client_mac" value="<?= $data['client_mac'] ?>" class="form-control"></div>
</div>

<div class="row form-group">
  <label for="loc" class="col-sm-4 control-label">Home Location</label>
  <div class="col-sm-8">
  <select name="loc" id="loc">
<?php foreach ( $data['locations'] as $loc ) { ?>
  <option value="<?= $loc['id'] ?>"<?= empty($loc['selected']) ? "" : " selected " ?>><?= $loc['name'] ?></option>
<?php } ?>
  </select>
  </div>
</div>

<div class="row form-group">
<label for="desc" class="col-sm-4 control-label">Description</label>
<div class="col-sm-8"><input type="text" id="desc" name="desc" value="<?= $data['desc'] ?>" class="form-control"></div>
</div>

<div class="row form-group">
<input type="submit" name="op" value="Set" class="btn">
</div>
</div>
</form>

</div>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
