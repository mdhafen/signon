<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Google Sync Check Search</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<h2>Search for SignOn object for the Google Sync Check</h2>

<div class="panel panel-default panel-body">
<div class="container-fluid">
<form action='sync_check.php'>
<div class="row form-group">
<div class="col-sm-4">
    <label for="query" class="control-label">Username or Student/Employee Number</label>
</div>
<div class="col-sm-8">
    <input type="text" id="query" name="query" class="form-control">
</div>
</div>

<div class="row">
<input type="submit" class="btn btn-default" value="Search">
<a class="btn btn-default" href="index.php">Back</a>
</div>

</form>

</div>
</div>

</div>
</div>

<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-sync_check');
    $(el).addClass('active');

  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
