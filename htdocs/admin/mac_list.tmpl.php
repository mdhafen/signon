<?php include($data['_config']['base_dir'] .'/htdocs/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Register Lab Device</title>
<link rel="stylesheet" type="text/css" href="<?= $data['_config']['base_url'] ?>css/datatables.min.css"/>
<script type="text/javascript">
$(document).ready(function() {
	$('body .dropdown-toggle').dropdown();
});
</script>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1>Search</h1>

<div class="panel panel-default panel-body">
<div class="container-fluid">
<form method="post" action="register.php?op=List" class="form-horizontal">

<div class="row form-group">
<div class="col-sm-4">
  <label for="query" class="control-label sr-only">Enter a search term:</label>
</div>
	<div class="col-sm-8"><input type="query" id="search_term" name="search_term" class="form-control"></div>
</div>

<div class="row form-group">
<input type="submit" value="Search" class="btn">
</div>

<div class="row form-group help-block">
    <p>Wifi Categories: Staff, Student, Guest, Trusted, Other, Confinement</p>
</div>

</form>
</div>
</div>

<h1>List Of Registered Lab Device</h1>

<?php if ( ! empty($data['deleted']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<p>M.A.C. removed from register!</p>
</div>
<?php } ?>

<div class="panel panel-default panel-body">
<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>
<th>M.A.C. Address</th>
<th>Registered By</th>
<th>Device Home</th>
<th>Device Description</th>
<th>Date Registered</th>
<th></th>
</tr>
</thead>
<tbody>
<?php foreach ( $data['mac_list'] as $mac_row ) { ?>

<tr>
<td><?= $mac_row['macaddress'] ?></td>
<td><?= $mac_row['submitted_user'] ?></td>
<td><?= $mac_row['device_home'] ?></td>
<td><?= $mac_row['submitted_desc'] ?></td>
<td><?= $mac_row['submitted_date'] ?></td>
<td>
  <form method="post" action="register.php" class="form-horizontal" method="post" enctype="multipart/form-data">
  <input type="hidden" name="client_mac" value="<?= $mac_row['macaddress'] ?>">
  <input type="submit" name="op" value="Delete" class="form-control">
  </form>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>

</div>
</div>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
