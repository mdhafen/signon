<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Add</title>
<script type="text/javascript">
function update_dn(val,rid) {
         var dn_el = document.getElementById('dn');
         var parent_el = document.getElementById('parent');
         dn_el.value = rid + "=" + val + "," + parent_el.value;
}

function add_field( btn, attr_num ) {
  var newInput = document.createElement("input");
  newInput.setAttribute("type","text");
  newInput.setAttribute("class","form-control");
  newInput.setAttribute("name",attr_num+"_val[]");
  btn.parentNode.insertBefore( newInput, btn )
}
</script>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
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
<input type="text" id="<?= $count ?>_val" name="<?= $count ?>_val[]" value=""<?= !empty($data['defaults'][$attr]) ? ' placeholder="'. $data['defaults'][$attr] .'"' : '' ?><?php if ( $data['rid'] == $attr ) { ?> onchange="update_dn(this.value,'<?= $attr ?>')"<?php } ?> class="form-control" required="true">
<?php
   if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) {
?>
<input type="button" class="btn btn-default" value="+" onclick="add_field(this,'<?= $count ?>')">
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
<input type="text" id="<?= $count ?>_val" name="<?= $count ?>_val[]" value=""<?= !empty($data['defaults'][$attr]) ? ' placeholder="'. $data['defaults'][$attr] .'"' : '' ?> class="form-control">
<?php   if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
<input type="button" class="btn btn-default" value="+" onclick="add_field(this,'<?= $count ?>')">
<?php   } ?>
</div>
</div>
<?php
        $count++;
    }
?>

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
</div>
</div>

<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-manage');
    $(el).addClass('active');
  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
