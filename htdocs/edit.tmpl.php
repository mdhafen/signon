<?php include( 'doc-open.php' ); ?>
<title>Staff Login Management - Edit</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
include( 'doc-menu.php' );
?>

<h1><?= $data['object_dn'] ?></h1>
<form method="post" action="save.php">
<div>
<?php
$count = 1;
foreach ( $data['must'] as $attr ) {
?>
<div class='object_attribute'><span class="object_attr_key important"><?= $attr ?>:</span>
<input name="<?= $count ?>_attr" type="hidden" value="<?= $attr ?>">
<?php   if ( ! empty($data['object'][$attr]) ) {
            if ( count($data['object'][$attr]) > 1 ) { ?>
<div class="object_attr_values">
<?php       }
            foreach ( $data['object'][$attr] as $val ) { ?>
<span class="object_attr_value">
<input name="<?= $count ?>_val[]" value="<?= $val ?>">
<input name="<?= $count ?>_orig[]" type="hidden" value="<?= $val ?>">
</span>
<?php       }
            if ( count($data['object'][$attr]) > 1 ) { ?>
</div>
<?php       }
        }
	else { ?>
<span class="object_attr_value">
<input name="<?= $count ?>_val[]" value="">
</span>
<?php   }
        if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
<input type="button" value="+" onclick="add_field('<?= $count ?>')">
<?php   } ?>
</div>
<?php
  $count++;
}
?>

<?php foreach ( $data['may'] as $attr ) { ?>
<div class='object_attribute'><span class="object_attr_key"><?= $attr ?>:</span>
<input name="<?= $count ?>_attr" type="hidden" value="<?= $attr ?>">
<?php   if ( ! empty($data['object'][$attr]) ) {
            if ( count($data['object'][$attr]) > 1 ) { ?>
<div class="object_attr_values">
<?php       }
            foreach ( $data['object'][$attr] as $val ) { ?>
<span class="object_attr_value">
<input name="<?= $count ?>_val[]" value="<?= $val ?>">
<input name="<?= $count ?>_orig[]" type="hidden" value="<?= $val ?>">
</span>
<?php       }
            if ( count($data['object'][$attr]) > 1 ) { ?>
</div>
<?php       }
        }
	else { ?>
<span class="object_attr_value">
<input name="<?= $count ?>_val[]" value="">
</span>
<?php   }
        if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
<input type="button" value="+" onclick="add_field('<?= $count ?>')">
<?php   } ?>
</div>
<?php
        $count++;
    }
?>
</div>

<div>
<input type="submit" name="action" value="Update">
<input type="hidden" name="count" value="<?= $count ?>">
<input type="hidden" name="dn" value="<?= $data['object_dn'] ?>">
<a href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Cancel</a>
</div>
</form>

<?php include( 'doc-close.php' ); ?>
