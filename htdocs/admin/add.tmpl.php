<?php include( $data['_config']['base_dir'] .'/htdocs/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Add</title>
<script type="text/javascript">
function update_dn(val,rid) {
         var dn_el = document.getElementById('dn');
         var parent_el = document.getElementById('parent');
         dn_el.value = rid + "=" + val + "," + parent_el.value;
}
</script>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<h1>Add Entry</h1>
<form method="post" action="save.php">
<div>
<?php
$count = 1;
foreach ( $data['must'] as $attr ) {
?>
<div class='object_attribute'><label for="<?= $count ?>_val" class="object_attr_key important"><?= $attr ?>:</label>
<input name="<?= $count ?>_attr" type="hidden" value="<?= $attr ?>">
<?php
   if ( $attr == 'objectClass' ) {
?>
<div class="object_attr_values">
<?php
      foreach ( $data['classes'] as $class ) {
?>
<span class="object_attr_value">
<input name="<?= $count ?>_val[]" value="<?= $class ?>">
</span>
<?php
      }
?>
</div>
<?php } else { ?>
<span class="object_attr_value">
<input id="<?= $count ?>_val" name="<?= $count ?>_val[]" value=""<?php if ( $data['rid'] == $attr ) { ?> onchange="update_dn(this.value,'<?= $attr ?>')"<?php } ?>>
</span>
<?php   if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
<input type="button" value="+" onclick="add_field('<?= $count ?>')">
<?php
        }
   }
?>
</div>
<?php
        $count++;
}
?>

<?php foreach ( $data['may'] as $attr ) { ?>
<div class='object_attribute'><label for="<?= $count ?>_val" class="object_attr_key"><?= $attr ?>:</label>
<input name="<?= $count ?>_attr" type="hidden" value="<?= $attr ?>">
<span class="object_attr_value">
<input id="<?= $count ?>_val" name="<?= $count ?>_val[]" value="">
</span>
<?php   if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
<input type="button" value="+" onclick="add_field('<?= $count ?>')">
<?php   } ?>
</div>
<?php
        $count++;
    }
?>
</div>

<div>
<input type="submit" name="action" value="Add">
<input type="hidden" name="count" value="<?= $count ?>">
<input type="hidden" name="dn" id="dn" value="">
<input type="hidden" name="parent" id="parent" value="<?= $data['parent'] ?>">
<input type="hidden" name="classes" value="<?= implode(' ',$data['classes']) ?>">
<a href="object.php?dn=<?= urlencode($data['parent']) ?>">Cancel</a>
</div>
</form>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
