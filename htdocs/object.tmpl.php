<?php include( 'doc-open.php' ); ?>
<title>Staff Login Management - Details</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
include( 'doc-menu.php' );
?>

<h1><?= $data['object_dn'] ?></h1>
<div>
<?php foreach ( $data['object'] as $key => $vals ) { ?>
<div class='object_attribute'><span class="object_attr_key"><?= $key ?></span>
<?php   foreach ( $vals as $val ) { ?>
<div class="object_attr_value"><?= $val ?></div>
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
<?php } else if ( in_array('person',$child['objectClass']) ) { ?>
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

<?php include( 'doc-close.php' ); ?>
