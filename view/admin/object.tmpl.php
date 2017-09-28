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
  <div><?= $val ?></div>
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

<?php if ( ! empty($data['can_password']) && ! empty($data['is_person']) ) { ?>
<a class="btn btn-default" href="password.php?dn=<?= urlencode($data['object_dn']) ?>">Reset Password</a>
<?php   if ( stripos($data['object_dn'],',ou=students,') !== FALSE ) { ?>
<a class="btn btn-default" href="password.php?default=1&amp;dn=<?= urlencode($data['object_dn']) ?>">Set Password to Default</a>
<?php   } ?>
<span>User is <?= ($data['object']['businessCategory'][0] != 'Confinement' && $data['object']['businessCategory'][0] != 'Banned' ? 'Not' : '' ) ?> WiFi Confined <?php if ($data['object']['businessCategory'][0] != 'Banned') { ?><a class="btn btn-default" href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;toggle=<?= ($data['object']['businessCategory'][0] != 'Confinement' ? 'on' : 'off') ?>">Toggle</a><?php } ?></span>
<span>User is <?= ($data['object']['businessCategory'][0] != 'Banned' ? 'Not' : '' ) ?> Banned from gina Access <a class="btn btn-default" href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;class=Banned&amp;toggle=<?= ($data['object']['businessCategory'][0] != 'Banned' ? 'on' : 'off') ?>">Toggle</a></span>
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

<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
