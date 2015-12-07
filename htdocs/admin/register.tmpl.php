<?php include($data['_config']['base_dir'] .'/htdocs/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Register Lab Device</title>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1>Register Lab Device</h1>
<h3><a href="register.php?op=List">See registered M.A.C.&apos;s</a></h3>

<?php if ( ! empty($data['error']) ) { ?>
<div class="alert alert-danger" role="alert">
There was an error! <?= $data['err_msg'] ?>
</div>
<?php } ?>

<?php if ( ! empty($data['success']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<p>Success!</p>
<p>Now you can connect to the WCSDlabs wireless network with the pass-phrase "theskyisfalling2day!".</p>
</div>
<?php } ?>

<form method="post" action="register.php" class="form-horizontal" method="post" enctype="multipart/form-data">
<div class="panel panel-default panel-body">
<div class="container-fluid">

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
<div class="col-sm-8">
 <textarea id="desc" name="desc" rows="3" cols="40" class="form-control"><?= $data['desc'] ?></textarea>
</div>
</div>

<div class="row form-group">
<input type="submit" name="op" value="Register" class="btn">
</div>

</div>
</div>

<h2>Or...</h2>

<div class="panel panel-default panel-body">
<div class="container-fluid">

<div class="row form-group">
<label for="importfile" class="col-sm-4 control-label">CSV Import File</label>
<div class="col-sm-8"><input type="file" id="importfile" name="importfile" class="form-control" style="height:auto;"></div>
</div>

<div class="row form-group">
<label for="drop_first" class="col-sm-4 control-label">Discard first row</label>
<div class="col-sm-8"><input type="checkbox" id="drop_first" name="drop_first" checked='checked'></div>
</div>

<div class="row form-group">
<label for="mac_column" class="col-sm-4 control-label">Column of M.A.C. Address</label>
<div class="col-sm-8"><input type="text" id="mac_column" name="mac_column" placeholder="1" class="form-control"></div>
</div>

<div class="row form-group">
<label for="desc_column" class="col-sm-4 control-label">Column of Description</label>
<div class="col-sm-8">
  <input type="text" id="desc_column" name="desc_column" placeholder="2" class="form-control">
  <span class="help-block">Uses Description field above as a default value.</span>
</div>
</div>

<div class="row form-group">
<label for="loc_column" class="col-sm-4 control-label">Column of Location Number</label>
<div class="col-sm-8">
  <input type="text" id="loc_column" name="loc_column" placeholder="3" class="form-control">
  <span class="help-block">Uses Location drop-down above as a default value.</span>
</div>
</div>

<div class="row form-group">
<input type="submit" name="op" value="Import" class="btn">
</div>

</div>
</div>
</form>

</div>
</div>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
