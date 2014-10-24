<?php include( 'doc-open.php' ); ?>
<title>Welcome to Staff Login Management</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
include( 'doc-menu.php' );
?>

<div>
<?php foreach ( $data['folders'] as $ou ) { ?>
<div class='folder' data="<?= $ou['dn'] ?>"><span class="tree_control">?</span><a href="object.php?dn=<?= urlencode($ou['dn']) ?>"><?= $ou['ou'] ?></a></div>
<?php } ?>
</div>

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
