<?php include( 'doc-open.php' ); ?>
<title>Staff Login Management - Edit</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
include( 'doc-menu.php' );
?>

<h1><?= $data['object_dn'] ?></h1>
<div>
<?php foreach ( $data['must'] as $attr ) { ?>
<div class='object_attribute'><span class="object_attr_key important"><?= $attr ?> value:</span>
<?php   if ( ! empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
(Single Value)
<?php   } ?>
<?php   if ( ! empty($data['object'][$attr]) ) {
            foreach ( $data['object'][$attr] as $val ) { ?>
<div class="object_attr_value"><?= $val ?></div>
<?php       }
        } ?>
</div>
<?php } ?>

<?php foreach ( $data['may'] as $attr ) { ?>
<div class='object_attribute'><span class="object_attr_key"><?= $attr ?> value:</span>
<?php   if ( ! empty($data['object'][$attr]) ) {
            foreach ( $data['object'][$attr] as $val ) { ?>
<div class="object_attr_value"><?= $val ?></div>
<?php       }
        } ?>
</div>
<?php } ?>
</div>

<div>
<a href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Cancel</a>
</div>

<?php include( 'doc-close.php' ); ?>
