<?php include($data['_config']['base_dir'] .'/view/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Register Lab Device</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1>Register Lab Device</h1>
<h3><a href="register.php?op=List">Search registered M.A.C.&apos;s</a></h3>

<h1 class="bg-danger">Fields and IoT Categories are not yet implemented!</h1>

<?php if ( ! empty($data['error']) ) { ?>
<div class="alert alert-danger" role="alert">
There was an error! <?= $data['err_msg'] ?>
</div>
<?php } ?>

<!--
<?php if ( ! empty($data['success']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<p>Success!</p>
<p>Now you can connect to the WCSDlabs wireless network with the pass-phrase "theskyisfalling2day!".</p>
</div>
<?php } ?>
-->

<form method="post" action="register.php" class="form-horizontal" method="post" enctype="multipart/form-data">
<div class="panel panel-default panel-body">
<div class="container-fluid">

<div class="row form-group">
<label for="client_mac" class="col-sm-4 control-label">M.A.C. Address</label>
<div class="col-sm-8"><input type="text" id="client_mac" name="client_mac" value="<?= !empty($data['client_mac']) ? $data['client_mac'] : '' ?>" class="form-control"></div>
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

<!--
<div class="row form-group">
  <label for="labs_cat" class="col-sm-4 control-label">Labs Category</label>
  <div class="col-sm-8">
  <select name="labs_cat" id="cat">
      <option value="">No Access</option>
      <option value="Labs"<?= empty($data['labs_cat']) || $data['labs_cat'] == 'Labs' ? " selected":"" ?>>Lab device</option>
      <option value="Lan"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Lan' ? " selected":""?>>Lan device</option>
      <option value="Staff"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Staff' ? " selected":""?>>Staff device</option>
      <option value="Facilities"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Facilities' ? " selected":""?>>Facilities device</option>
      <option value="AV"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'AV' ? " selected":""?>>Audio / Visual Devices</option>
      <option value="Phone"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Phone' ? " selected":""?>>Phone</option>
      <option value="TechOffice"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'TechOffice' ? " selected":""?>>Tech Office Only</option>
      <option value="Guest"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Guest' ? " selected":""?>>Guest / untrusted device</option>
  </select>
  <span class="help-block">This field effects device access to school and district networks.  Only change this if you have been told to by Technology Department staff.</span>
  </div>
</div>
-->

<div class="row form-group">
  <label for="fields_cat" class="col-sm-4 control-label">Fields Category</label>
  <div class="col-sm-8">
  <select name="fields_cat" id="cat">
      <option value="">No Access</option>
      <option value="Facilities"<?= !empty($data['fields_cat']) && $data['fields_cat'] == 'Facilities' ? " selected":""?>>Facilities device</option>
  </select>
  <span class="help-block">This field effects device access to the WCSDfields network.  Only change this if you have been told to by Technology Department staff.</span>
  </div>
</div>

<div class="row form-group">
  <label for="iot_cat" class="col-sm-4 control-label">IoT Category</label>
  <div class="col-sm-8">
  <select name="iot_cat" id="cat">
      <option value="">No Access</option>
     <option value="Lan"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'Lan' ? " selected":""?>>Staff device</option>
      <option value="Facilities"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'Facilities' ? " selected":""?>>Facilities device</option>
      <option value="Student"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'Student' ? " selected":"" ?>>Student device</option>
      <option value="Phone"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'Phone' ? " selected":""?>>Staff Phone</option>
      <option value="Camera"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'Camera' ? " selected":""?>>Camera</option>
      <option value="CyberCorp"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'CyberCorp' ? " selected":""?>>CyberCorp device</option>
  </select>
  <span class="help-block">This field effects device access to the WCSDiot network.  Only change this if you have been told to by Technology Department staff.</span>
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
<div class="col-sm-8"><input type="text" id="mac_column" name="mac_column" placeholder="for example: 1" class="form-control"></div>
</div>

<div class="row form-group">
<label for="loc_column" class="col-sm-4 control-label">Column of Location Number</label>
<div class="col-sm-8">
  <input type="text" id="loc_column" name="loc_column" placeholder="for example: 2" class="form-control">
  <span class="help-block">Uses Location drop-down above as a default value.</span>
</div>
</div>

<div class="row form-group">
<label for="desc_column" class="col-sm-4 control-label">Column of Description</label>
<div class="col-sm-8">
  <input type="text" id="desc_column" name="desc_column" placeholder="for example: 3" class="form-control">
  <span class="help-block">Uses Description field above as a default value.</span>
</div>
</div>

<!--
<div class="row form-group">
<label for="lab_cat_column" class="col-sm-4 control-label">Column of Labs Category</label>
<div class="col-sm-8">
  <input type="text" id="lab_cat_column" name="lab_cat_column" placeholder="for example: 4" class="form-control">
  <span class="help-block">Uses labs category field above as a default value.</span>
</div>
</div>
-->

<div class="row form-group">
<label for="field_cat_column" class="col-sm-4 control-label">Column of Fields Category</label>
<div class="col-sm-8">
  <input type="text" id="field_cat_column" name="field_cat_column" placeholder="for example: 5" class="form-control">
  <span class="help-block">Uses fields category field above as a default value.</span>
</div>
</div>

<div class="row form-group">
<label for="iot_cat_column" class="col-sm-4 control-label">Column of IoT Category</label>
<div class="col-sm-8">
  <input type="text" id="iot_cat_column" name="iot_cat_column" placeholder="for example: 6" class="form-control">
  <span class="help-block">Uses iot category field above as a default value.</span>
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

<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-register');
    $(el).addClass('active');
  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
