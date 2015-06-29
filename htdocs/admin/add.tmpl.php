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

<div class="main-page">

<h1>Add Entry</h1>
<div class="panel panel-default panel-body">
<div class="container-fluid">
<form method="post" action="save.php" class="form-horizontal">
<?php
$count = 1;
?>
<div class='row form-group'><label for="<?= $count ?>_val" class="col-sm-4 control-label important">objectClass:</label>
  <input name="<?= $count ?>_attr" type="hidden" value="objectClass">
  <div class="col-sm-8">
<?php
foreach ( $data['classes'] as $class ) {
?>
  <input type="text" name="<?= $count ?>_val[]" value="<?= $class ?>" class="form-control">
<?php
}
?>
  </div>
</div>
<?php
$count++;
foreach ( $data['must'] as $attr ) {
   if ( $attr == 'objectClass' ) {
     continue;
   }
?>
<div class='row form-group'><label for="<?= $count ?>_val" class="col-sm-4 control-label important"><?= $attr ?>:</label>
<input name="<?= $count ?>_attr" type="hidden" value="<?= $attr ?>">
<div class="col-sm-8">
<input type="text" id="<?= $count ?>_val" name="<?= $count ?>_val[]" value=""<?php if ( $data['rid'] == $attr ) { ?> onchange="update_dn(this.value,'<?= $attr ?>')"<?php } ?> class="form-control">
<?php
   if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) {
?>
<input type="button" class="btn btn-default" value="+" onclick="add_field('<?= $count ?>')">
<?php
   }
?>
</div>
</div>
<?php
   $count++;
}
?>

<?php foreach ( $data['may'] as $attr ) { ?>
<div class='row form-group'><label for="<?= $count ?>_val" class="col-sm-4 control-label"><?= $attr ?>:</label>
<input name="<?= $count ?>_attr" type="hidden" value="<?= $attr ?>">
<div class="col-sm-8">
<input type="text" id="<?= $count ?>_val" name="<?= $count ?>_val[]" value="" class="form-control">
<?php   if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
<input type="button" class="btn btn-default" value="+" onclick="add_field('<?= $count ?>')">
<?php   } ?>
</div>
</div>
<?php
        $count++;
    }
?>
</div>

<div>
<input type="submit" name="action" value="Add" class="btn btn-default">
<input type="hidden" name="count" value="<?= $count ?>">
<input type="hidden" name="dn" id="dn" value="">
<input type="hidden" name="parent" id="parent" value="<?= $data['parent'] ?>">
<input type="hidden" name="classes" value="<?= implode(' ',$data['classes']) ?>">
<a class="btn btn-default" href="object.php?dn=<?= urlencode($data['parent']) ?>">Cancel</a>
</div>
</form>

</div>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
