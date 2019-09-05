<?php include($data['_config']['base_dir'] .'/view/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Update Lab Device Registration</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1>Update Lab Device Registration</h1>
<?php if ( ! empty($data['error']) ) { ?>
<div class="alert alert-danger" role="alert">
There was an error! <?= $data['err_msg'] ?>
</div>
<?php } ?>

<h1 class="bg-danger">Fields and IoT Categories are not yet implemented!</h1>

<form method="post" action="register.php" class="form-horizontal" method="post" enctype="multipart/form-data">
<input type="hidden" name="client_mac" value="<?= $data['client_mac'] ?>">
<div class="panel panel-default panel-body">
<div class="container-fluid">

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
  <label for="labs_cat" class="col-sm-4 control-label">Labs Category</label>
  <div class="col-sm-8">
  <select name="labs_cat" id="labs_cat">
      <option value="">No Access</option>
      <option value="Labs"<?= empty($data['labs_cat']) || $data['labs_cat'] == 'Labs' ? " selected":"" ?>>Lab device</option>
     <option value="Lan"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Lan' ? " selected":""?>>Staff device</option>
      <option value="Facilities"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Facilities' ? " selected":""?>>Facilities device</option>
      <option value="AV"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'AV' ? " selected":""?>>Audio / Visual Devices</option>
      <option value="Phone"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Phone' ? " selected":""?>>Phone</option>
      <option value="TechOffice"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'TechOffice' ? " selected":""?>>Tech Office Only</option>
      <option value="Guest"<?= !empty($data['labs_cat']) && $data['labs_cat'] == 'Guest' ? " selected":""?>>Guest / untrusted device</option>
  </select>
  <span class="help-block">This field effects device access to school and district networks.  Only change this if you have been told to by Technology Department staff.</span>
  </div>
</div>

<div class="row form-group">
  <label for="fields_cat" class="col-sm-4 control-label">Fields Category</label>
  <div class="col-sm-8">
  <select name="fields_cat" id="fields_cat">
      <option value="">No Access</option>
      <option value="Facilities"<?= !empty($data['fields_cat']) && $data['fields_cat'] == 'Facilities' ? " selected":""?>>Facilities device</option>
  </select>
  <span class="help-block">This field effects device access to the WCSDfields network.  Only change this if you have been told to by Technology Department staff.</span>
  </div>
</div>

<div class="row form-group">
  <label for="iot_cat" class="col-sm-4 control-label">IoT Category</label>
  <div class="col-sm-8">
  <select name="iot_cat" id="iot_cat">
      <option value="">No Access</option>
      <option value="Labs"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'Labs' ? " selected":"" ?>>Lab device</option>
     <option value="Lan"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'Lan' ? " selected":""?>>Staff device</option>
      <option value="Facilities"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'Facilities' ? " selected":""?>>Facilities device</option>
      <option value="AV"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'AV' ? " selected":""?>>Audio / Visual Devices</option>
      <option value="TechOffice"<?= !empty($data['iot_cat']) && $data['iot_cat'] == 'TechOffice' ? " selected":""?>>Tech Office Only</option>
  </select>
  <span class="help-block">This field effects device access to the WCSDiot network.  Only change this if you have been told to by Technology Department staff.</span>
  </div>
</div>

<div class="row form-group">
<input type="submit" name="op" value="Save" class="btn">
<a class="btn" href="register.php?op=List&search_term=<?= urlencode($data['search_term']) ?>">Cancel</a>
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
