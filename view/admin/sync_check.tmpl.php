<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Google Sync Check</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<h2>Check Google and SignOn are in Sync</h2>

<div class="panel panel-default panel-body">
<div class="container-fluid">
<form action='do_google_sync.php' method='post'>
<input type="hidden" name="dn" value="<?= htmlspecialchars($data['object_dn'],ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?>">
<?php if ( !empty($data['mail']) ) { ?>
<input type="hidden" name="mail" value="<?= $data['mail'] ?>">
<?php } ?>
<input type="hidden" name="ldap_duplicates" value="<?= !empty($data['ldap_duplicates']) ? 1 : 0 ?>">
<?php for ( $i = 1; $i < count( $data['diff'][1] ); $i++ ) { ?>
<?php     if ( $data['diff'][0][$i] == 'LDAP' ) { ?>
<input type="hidden" name="dns[]" value="<?= htmlspecialchars($data['diff'][1][$i],ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?>">
<?php     } ?>
<?php } ?>
<table class="table table-bordered table-striped">
<tbody>
<?php foreach ( $data['diff'] as $line ) { ?>
<tr>
<?php   foreach ( $line as $val ) { ?>
  <td><?= htmlspecialchars($val,ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?></td>
<?php   } ?>
</tr>
<?php } ?>
<?php if ( !empty($data['can_edit']) && !empty($data['ldap_duplicates']) ) { ?>
<tr>
<td>_Object to keep</td>
<?php for ( $i = 1; $i < count( $data['diff'][1] ); $i++ ) { ?>
<td>
<?php     if ( $data['diff'][0][$i] == 'LDAP' ) { ?>
<input type="radio" name="primary" value="<?= urlencode($data['diff'][1][$i]) ?>">
<?php     } ?>
</td>
<?php } ?>
</tr>
<?php } ?>
</table>

<div class="row">
<a class="btn btn-default" href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Up</a>

<?php if ( ! empty( $data['can_edit'] ) ) { ?>
<input type="submit" class="btn btn-default" value="Sync">
<?php } ?>
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
