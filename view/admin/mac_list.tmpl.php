<?php include($data['_config']['base_dir'] .'/view/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Register Lab Device</title>
<link rel="stylesheet" type="text/css" href="<?= $data['_config']['base_url'] ?>css/datatables.min.css"/>
<script type="text/javascript">
$(document).ready(function() {
	$('body .dropdown-toggle').dropdown();
});
</script>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1>Search</h1>

<div class="panel panel-default panel-body">
<div class="container-fluid">
<form method="post" action="register.php" class="form-horizontal">
<input type="hidden" name="op" value="List">
<div class="row form-group">
<div class="col-sm-4">
  <label for="query" class="control-label">Enter a search term:</label>
</div>
	<div class="col-sm-8"><input type="query" id="search_term" name="search_term" class="form-control"></div>
</div>

<div class="row form-group">
<input type="submit" value="Search" class="btn">
</div>

</form>
</div>
</div>

<h1>List Of Registered Lab Devices</h1>

<?php if ( ! empty($data['deleted']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<p>M.A.C. address removed from register!</p>
</div>
<?php } ?>
<?php if ( ! empty($data['edited']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<p>M.A.C. address updated in register!</p>
</div>
<?php } ?>

<div class="panel panel-default panel-body">
<div class="table-responsive">
<table class="table table-striped">
<thead>
<tr>
<th>M.A.C. Address</th>
<th>Registered / Updated By</th>
<th>Device Home</th>
<th>Device Description</th>
<th>Labs Category</th>
<th>Fields Category</th>
<th>IoT Category</th>
<th>Date Registered / Updated</th>
<th></th>
</tr>
</thead>
<tbody>
<?php
    foreach ( $data['mac_list'] as $mac_row ) {
        if ( empty($mac_row) ) { continue; }
        $log = !empty($mac_row['logs'])?array_pop($mac_row['logs']):['submitted_user'=>'','submitted_date'=>''];
?>

<tr>
<td><?= $mac_row['macaddress'] ?></td>
<td><?= $log['submitted_user'] ?></td>
<td><?= $mac_row['device_home'] ?></td>
<td><?= $mac_row['submitted_desc'] ?></td>
<td><?= $mac_row['labs_category'] ?></td>
<td><?= $mac_row['fields_category'] ?></td>
<td><?= $mac_row['iot_category'] ?></td>
<td><?= $log['submitted_date'] ?></td>
<td>
   <form method="post" action="register.php" class="form-horizontal" method="post" enctype="multipart/form-data">
  <input type="hidden" name="client_mac" value="<?= $mac_row['macaddress'] ?>">
  <input type="hidden" name="search_term" value="<?= $data['search_term'] ?>">
  <input type="submit" name="op" value="Delete" class="form-control">
  </form>

   <form method="post" action="register.php" class="form-horizontal" method="post" enctype="multipart/form-data">
  <input type="hidden" name="client_mac" value="<?= $mac_row['macaddress'] ?>">
  <input type="hidden" name="search_term" value="<?= $data['search_term'] ?>">
  <input type="submit" name="op" value="Edit" class="form-control">
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

<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-register');
    $(el).addClass('active');
  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
