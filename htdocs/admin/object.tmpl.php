<?php include( $data['_config']['base_dir'] .'/htdocs/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Details</title>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<div class="panel panel-default panel-body">
<div class="container-fluid">
<?php foreach ( $data['object'] as $key => $vals ) { ?>
<p class="row">
<div class="col-sm-4"><?= $key ?>:</div><div class="col-sm-8">
<?php   foreach ( $vals as $val ) { ?>
<div><?= $val ?></div>
<?php   } ?>
</div></p>
<?php } ?>

<?php if ( $data['parentdn'] ) { ?>
<div>
<a class="btn btn-default" href="object.php?dn=<?= urlencode($data['parentdn']) ?>">Up</a>
</div>
<?php } else { ?>
<div>
<a class="btn btn-default" href="index.php">Up</a>
</div>
<?php } ?>

<?php if ( ! empty( $data['can_edit'] ) ) { ?>
<div>
<a class="btn btn-default" href="edit.php?dn=<?= urlencode($data['object_dn']) ?>">Edit</a>
</div>
<?php if ( empty($data['is_person']) ) { ?>
<div>
Add:
<a class="btn btn-default" href="add.php?class=security&amp;parent=<?= urlencode($data['object_dn']) ?>">Security Account</a>
<a class="btn btn-default" href="add.php?class=user&amp;parent=<?= urlencode($data['object_dn']) ?>">User</a>
<a class="btn btn-default" href="add.php?class=group&amp;parent=<?= urlencode($data['object_dn']) ?>">Group</a>
<a class="btn btn-default" href="add.php?class=folder&amp;parent=<?= urlencode($data['object_dn']) ?>">Folder</a>
</div>
<?php }
  } ?>

<?php if ( ! empty($data['can_password']) && ! empty($data['is_person']) ) { ?>
<div>
<a class="btn btn-default" href="password.php?dn=<?= urlencode($data['object_dn']) ?>">Reset Password</a>
</div>
<?php } ?>
</div><!-- Object Panel -->

<?php if ( count( $data['children'] ) ) { ?>
<h2>Children</h2>
<div class="panel panel-default panel-body">
<?php foreach ( $data['children'] as $child ) { ?>
<p>
<a href="object.php?dn=<?= urlencode($child['dn']) ?>"><?= empty($child['cn']) ? $child['ou'][0] : $child['cn'][0] ?></a><span class="badge">
<?php if ( in_array('inetOrgPerson',$child['objectClass']) ) { ?>
(Person)
<?php } else if ( in_array('person',$child['objectClass']) || in_array('simpleSecurityObject',$child['objectClass']) ) { ?>
(System Account)
<?php } else if ( in_array('groupOfNames',$child['objectClass']) ) { ?>
(Group)
<?php } else if ( in_array('organizationalUnit',$child['objectClass']) ) { ?>
(Folder)
<?php } ?>
</span></p>
<?php } ?>
</div>
<?php } ?>

</div>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
