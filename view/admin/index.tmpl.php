<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Management</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class='main-page'>

<h1>
<?= $data['root'] ?>
</h1>

<div class="panel panel-default panel-body">
<div class="container-fluid">
<div class="row">
<?php if ( ! empty( $data['can_edit'] ) ) { ?>
Add:
<a class="btn btn-default" href="add.php?class=security&amp;parent=<?= urlencode($data['root']) ?>">Security Account</a>
<a class="btn btn-default" href="add.php?class=user&amp;parent=<?= urlencode($data['root']) ?>">User</a>
<a class="btn btn-default" href="add.php?class=group&amp;parent=<?= urlencode($data['root']) ?>">Group</a>
<a class="btn btn-default" href="add.php?class=folder&amp;parent=<?= urlencode($data['root']) ?>">Folder</a>
<?php } ?>
</div>

</div>
</div><!-- Object Panel -->

<?php if ( count( $data['children'] ) ) { ?>
<h2>Children</h2>
<div class="panel panel-default panel-body">
<?php foreach ( $data['children'] as $child ) { ?>
<div>
<a href="object.php?dn=<?= urlencode($child['dn']) ?>"><?= empty($child['cn']) ? $child['ou'][0] : $child['cn'][0] ?></a> <span class="badge">
<?php if ( in_array('inetOrgPerson',$child['objectClass']) ) { ?>
(Person)
<?php } else if ( in_array('person',$child['objectClass']) || in_array('simpleSecurityObject',$child['objectClass']) ) { ?>
(System Account)
<?php } else if ( in_array('groupOfNames',$child['objectClass']) ) { ?>
(Group)
<?php } else if ( in_array('organizationalUnit',$child['objectClass']) ) { ?>
(Folder)
<?php } ?>
</span></div>
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
