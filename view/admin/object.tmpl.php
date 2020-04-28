<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Details</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<div class="panel panel-default panel-body">
<div class="container-fluid">
<?php foreach ( $data['object'] as $key => $vals ) { ?>
<div class="row">
<div class="col-sm-4 text-right"><?= $key ?>:</div>
<div class="col-sm-8">
<?php   foreach ( $vals as $val ) { ?>
  <div><?= htmlspecialchars($val,ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?></div>
<?php   } ?>
<?php   if ( !empty($data['attr_changes'][$key]) ) { ?>
  <div class="text-muted">Last changed <?= date('m/d/Y g:i a',strtotime($data['attr_changes'][$key]['timestamp'])) ?> by <?= $data['attr_changes'][$key]['user'] ?></div>
<?php   } ?>
</div>
</div>
<?php } ?>

<div class="row">
<?php if ( $data['parentdn'] ) { ?>
<a class="btn btn-default" href="object.php?dn=<?= urlencode($data['parentdn']) ?>">Up</a>
<?php } else { ?>
<a class="btn btn-default" href="index.php">Up</a>
<?php } ?>

<?php if ( ! empty( $data['can_edit'] ) ) { ?>
<a class="btn btn-default" href="edit.php?dn=<?= urlencode($data['object_dn']) ?>">Edit</a>
<a class="btn btn-warning" href="delete.php?dn=<?= urlencode($data['object_dn']) ?>">Delete</a>
<?php if ( empty($data['is_person']) ) { ?>
Add:
<a class="btn btn-default" href="add.php?class=security&amp;parent=<?= urlencode($data['object_dn']) ?>">Security Account</a>
<a class="btn btn-default" href="add.php?class=user&amp;parent=<?= urlencode($data['object_dn']) ?>">User</a>
<a class="btn btn-default" href="add.php?class=group&amp;parent=<?= urlencode($data['object_dn']) ?>">Group</a>
<a class="btn btn-default" href="add.php?class=folder&amp;parent=<?= urlencode($data['object_dn']) ?>">Folder</a>
<?php }
  } ?>

<?php
if ( ! empty($data['can_password']) && ! empty($data['is_person']) ) {
  if ( ! ( !empty($data['user_lock']) && ! $data['can_lock'] ) ) {
?>
<a class="btn btn-default" href="password.php?dn=<?= urlencode($data['object_dn']) ?>">Reset Password</a>
<?php   if ( stripos($data['object_dn'],',ou=students,') !== FALSE ) { ?>
<a class="btn btn-default" href="password.php?default=1&amp;dn=<?= urlencode($data['object_dn']) ?>">Set Password to Default</a>
<?php   } ?>
<?php }
      else {
?>
<a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;toggle=off&amp;class=Lock" class="btn btn-danger">User is Locked</a>
<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#lock_details">Show/Hide Lock Details</button>
<div class="collapse" id="lock_details"><div class="well">
Password: <?= $data['user_lock']['passwd'] ?><br>
Locked by: <?= $data['user_lock']['user'] ?><br>
Locked on: <?= $data['user_lock']['timestamp'] ?><br>
</div></div>
<?php } ?>
<div class="form-group">
<h4>Security</h4>
<a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;toggle=<?= ($data['object']['businessCategory'][0] != 'Confinement' && $data['object']['businessCategory'][0] != 'Banned' ? 'on' : 'off') ?>" <?= ($data['object']['businessCategory'][0] != 'Confinement' && $data['object']['businessCategory'][0] != 'Banned' ? 'class="btn btn-success">WiFi Access Enabled' : 'class="btn btn-danger">WiFi Access Disabled' ) ?></a>
<a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;class=Banned&amp;toggle=<?= ($data['object']['businessCategory'][0] != 'Banned' ? 'on' : 'off') ?>" <?= ($data['object']['businessCategory'][0] != 'Banned' ? 'class="btn btn-success">GinaAccess Logins Enabled' : 'class="btn btn-danger">GinaAccess Logins Disabled' ) ?></a>
     <a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;class=VPN&amp;toggle=<?= (!empty($data['object_vpn']) ? 'on' : 'off') ?>" <?= (!empty($data['object_vpn']) ? 'class="btn btn-success">VPN Access Enabled' : 'class="btn btn-danger">VPN Access Disabled' ) ?></a>
<?php if ( $data['can_lock'] ) { ?>
      <a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;toggle=on&amp;class=Lock" class="btn btn-success">User is Unlocked</a>
<?php } ?>
</div>
<?php } ?>
</div>

</div>
</div><!-- Object Panel -->

<?php if ( count( $data['children'] ) ) { ?>
<h2>Children</h2>
<div class="panel panel-default panel-body">
<?php foreach ( $data['children'] as $child ) { ?>
<p>
<a href="object.php?dn=<?= urlencode($child['dn']) ?>"><?php $rdn_attr = substr( $child['dn'], 0, strpos($child['dn'],'=') ); print $child[$rdn_attr][0]; ?></a> <span class="badge">
<?php if ( in_array('inetOrgPerson',$child['objectClass']) ) { ?>
(Person)
<?php } else if ( in_array('person',$child['objectClass']) || in_array('simpleSecurityObject',$child['objectClass']) ) { ?>
(System Account)
<?php } else if ( in_array('groupOfNames',$child['objectClass']) || in_array('posixGroup',$child['objectClass']) ) { ?>
(Group)
<?php } else if ( in_array('organizationalUnit',$child['objectClass']) ) { ?>
(Folder)
<?php } else { ?>
(Object)
<?php } ?>
</span></p>
<?php } ?>
</div>
<?php } ?>

</div>
</div>

<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-manage');
    $(el).addClass('active');
  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
