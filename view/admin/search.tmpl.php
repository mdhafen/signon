<?php include($data['_config']['base_dir'] .'/view/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Search</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1>Search</h1>

<div class="panel panel-default panel-body">
<div class="container-fluid">
<form method="post" action="search.php" class="form-horizontal">

<div class="row form-group">
<div class="col-sm-4">
  <label for="attrib" class="control-label sr-only">Choose attribute</label>
  <select name="attrib">
<?php foreach ( $data['attributes'] as $attr => $label ) { ?>
    <option value="<?= $attr ?>"><?= $label ?></option>
<?php } ?>
  </select>
</div>
<div class="col-sm-8"><input type="query" id="query" name="query" class="form-control"></div>
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

<?php if ( ! empty($data['search_results']) ) { ?>
<div class="panel panel-default panel-body">
<?php foreach ( $data['search_results'] as $user_dn ) { ?>
<div><a href="object.php?dn=<?= urlencode($user_dn) ?>"><?= $user_dn ?></a></div>
<?php } ?>
</div>
<?php } else if ( ! empty($data['no_results']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Nothing Found
</div>
<?php } ?>

</div>
</div>

<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
