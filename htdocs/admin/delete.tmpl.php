<?php include($data['_config']['base_dir'] .'/htdocs/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Delete Object</title>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>

<?php if ( !empty($data['children']) ) { ?>
<div class="alert alert-danger" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Object has child objects and so can not be deleted!
</div>

<form method="post" action="delete.php" class="form-horizontal">
<div class="panel panel-default panel-body">
<div class="container-fluid">

<div class="row form-group">
<p class="help-block has-warning">
  Are you sure you want to delete <?= $data['object_dn'] ?>?
  <?php if ( !empty($data['groups']) ) { ?>
  <br><?= $data['groups'] ?> Group memberships will also be cleared.
  <?php } ?>
</p>
</div>

<div class="row form-group">
<input type="submit" name="op" value="Delete" class="btn">
<input type="hidden" name="dn" value="<?= htmlspecialchars($data['object_dn']) ?>">
<a class="btn" href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Cancel</a>
</div>

</div>
</div>
</form>

</div>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
