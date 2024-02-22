<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Google Sync Check</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<h2>Results of Google Sync</h2>

<?php if ( ! empty($data['errors']) ) { ?>
<div class="alert alert-danger" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<?php   foreach ( $data['errors'] as $error ) { ?>
<p><?= $error ?></p>
<?php } ?>
</div>
<?php } ?>

<div class="panel panel-default panel-body">
<div class="container-fluid">
<?php if ( !empty($data['mods']) ) { ?>
<p>Attributes changed: <?= htmlspecialchars(implode(',',$data['mods']),ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?></p>
<?php } ?>
<?php if ( !empty($data['move']) ) { ?>
<p>Object was moved.</p>
<?php } ?>
<?php if ( !empty($data['add']) ) { ?>
<p>Object as created.</p>
<?php } ?>
<?php if ( !empty($data['password_set']) ) { ?>
<p>User password was set.</p>
<?php } ?>

<div class="row">
<a class="btn btn-default" href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Back</a>

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
