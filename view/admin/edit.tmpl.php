<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Edit</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<div class="panel panel-default panel-body">
<div class="container-fluid">
<form method="post" action="save.php" class="form-horizontal">
<?php
$count = 1;
foreach ( $data['must'] as $attr ) {
?>
<div class='row form-group'><label for="<?= $count ?>_val" class="col-sm-4 control-label important"><?= $attr ?>:</label>
<input name="<?= $count ?>_attr" type="hidden" value="<?= $attr ?>">
<div class="col-sm-8">
<?php   if ( ! empty($data['object'][$attr]) ) {
            foreach ( $data['object'][$attr] as $val ) { ?>
<input type="text" name="<?= $count ?>_val[]" value="<?= $val ?>" class="form-control">
<?php       }
        }
	else { ?>
<input type="text" name="<?= $count ?>_val[]" value="" class="form-control" required="true">
<?php   }
        if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
<input type="button" class="btn btn-default" value="+" onclick="add_field(this,'<?= $count ?>')">
<?php   } ?>
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
<?php   if ( ! empty($data['object'][$attr]) ) { ?>
<?php     foreach ( $data['object'][$attr] as $val ) { ?>
<?php       if ( $attr == 'businessCategory' ) { ?>
<select name="<?= $count ?>_val[]">
  <option value="Staff"<?= $val == 'Staff' ? " selected":"" ?>>Staff</option>
  <option value="Student"<?= $val == 'Student' ? " selected":"" ?>>Student</option>
  <option value="Guest"<?= $val == 'Guest' ? " selected":"" ?>>Guest</option>
  <option value="Trusted"<?= $val == 'Trusted' ? " selected":"" ?>>Trusted</option>
  <option value="Other"<?= $val == 'Other' ? " selected":"" ?>>Other</option>
  <option value="Confinement"<?= $val == 'Confinement' ? " selected":"" ?>>Confinement</option>
  <option value="Banned"<?= $val == 'Confinement' ? " selected":"" ?>>Banned</option>
</select>
<?php       } else { ?>
<input type="text" name="<?= $count ?>_val[]" value="<?= $val ?>" class="form-control">
<?php       } ?>
<?php     }
        }
        else { ?>
<?php     if ( $attr == 'businessCategory' ) { ?>
<select name="<?= $count ?>_val[]">
  <option value="Staff"<?= $val == 'Staff' ? " selected":"" ?>>Staff</option>
  <option value="Student"<?= $val == 'Student' ? " selected":"" ?>>Student</option>
  <option value="Guest"<?= $val == 'Guest' ? " selected":"" ?>>Guest</option>
  <option value="Trusted"<?= $val == 'Trusted' ? " selected":"" ?>>Trusted</option>
  <option value="Other"<?= $val == 'Other' ? " selected":"" ?>>Other</option>
  <option value="Confinement"<?= $val == 'Confinement' ? " selected":"" ?>>Confinement</option>
</select>
<?php     } else { ?>
<input type="text" name="<?= $count ?>_val[]" value="" class="form-control">
<?php     } ?>
<?php   }
        if ( empty($data['attrs'][$attr]['SINGLE-VALUE']) ) { ?>
<input type="button" class="btn btn-default" value="+" onclick="add_field(this,'<?= $count ?>')">
<?php   } ?>
</div>
</div>
<?php
        $count++;
    }
?>
</div>

<div>
<input type="submit" class="btn btn-default" name="action" value="Update">
<input type="hidden" name="count" value="<?= $count ?>">
<input type="hidden" name="dn" value="<?= $data['object_dn'] ?>">
<a class="btn btn-default" href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Cancel</a>
</div>
</form>

</div>
</div>

<script type="text/javascript">
function add_field( btn, attr_num ) {
  var newInput = document.createElement("input");
  newInput.setAttribute("type","text");
  newInput.setAttribute("class","form-control");
  newInput.setAttribute("name",attr_num+"_val[]");
  btn.parentNode.insertBefore( newInput, btn )
}
</script>

<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
