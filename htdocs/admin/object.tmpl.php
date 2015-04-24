<?php include( $data['_config']['base_dir'] .'/htdocs/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Details</title>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<h1><?= $data['object_dn'] ?></h1>
<div>
<?php foreach ( $data['object'] as $key => $vals ) { ?>
<div class='object_attribute'><span class="object_attr_key"><?= $key ?>:</span>
<?php   if ( count($vals) > 1 ) { ?>
<div class="object_attr_values">
<?php   } ?>
<?php   foreach ( $vals as $val ) { ?>
<span class="object_attr_value"><?= $val ?></span>
<?php   } ?>
<?php   if ( count($vals) > 1 ) { ?>
</div>
<?php   } ?>
</div>
<?php } ?>
</div>

<?php if ( $data['parentdn'] ) { ?>
<div>
<a href="object.php?dn=<?= urlencode($data['parentdn']) ?>">Up</a>
</div>
<?php } else { ?>
<div>
<a href="index.php">Up</a>
</div>
<?php } ?>

<?php if ( ! empty( $data['can_edit'] ) ) { ?>
<div>
<a href="edit.php?dn=<?= urlencode($data['object_dn']) ?>">Edit</a>
</div>
<div>
Add:
<a href="add.php?class=security&amp;parent=<?= urlencode($data['object_dn']) ?>">Security Account<a>
<a href="add.php?class=user&amp;parent=<?= urlencode($data['object_dn']) ?>">User</a>
<a href="add.php?class=group&amp;parent=<?= urlencode($data['object_dn']) ?>">Group</a>
<a href="add.php?class=folder&amp;parent=<?= urlencode($data['object_dn']) ?>">Folder</a>
</div>
<?php } ?>

<?php if ( ! empty($data['can_password']) && ! empty($data['is_person']) ) { ?>
<div>
<a href="password.php?dn=<?= urlencode($data['object_dn']) ?>">Reset Password</a>
</div>
<?php } ?>

<?php if ( count( $data['children'] ) ) { ?>
<h2>Children</h2>
<div>
<?php foreach ( $data['children'] as $child ) { ?>
<div>
<a href="object.php?dn=<?= urlencode($child['dn']) ?>"><?= empty($child['cn']) ? $child['ou'][0] : $child['cn'][0] ?></a>
<?php if ( in_array('inetOrgPerson',$child['objectClass']) ) { ?>
(Person)
<?php } else if ( in_array('person',$child['objectClass']) || in_array('simpleSecurityObject',$child['objectClass']) ) { ?>
(System Account)
<?php } else if ( in_array('groupOfNames',$child['objectClass']) ) { ?>
(Group)
<?php } else if ( in_array('organizationalUnit',$child['objectClass']) ) { ?>
(Folder)
<?php } ?>
</div>
<?php } ?>
</div>
<?php } ?>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
