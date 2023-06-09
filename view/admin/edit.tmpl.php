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
<form method="post" action="save.php" class="form-horizontal">
<div class="container-fluid">
<?php
$count = 1;
foreach ( $data['must'] as $attr ) {
?>
<div class='row form-group'><label for="<?= $count ?>_val" class="col-sm-4 control-label important"><?= $attr ?>:</label>
<input name="<?= $count ?>_attr" type="hidden" value="<?= $attr ?>">
<div class="col-sm-8">
<?php   if ( ! empty($data['object'][$attr]) ) {
            foreach ( $data['object'][$attr] as $val ) {
                if ( strlen($val) > 80 ) { ?>
<textarea id="<?= $count ?>_val" name="<?= $count ?>_val[]" class="form-control"><?= htmlspecialchars($val,ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?></textarea>
<?php           }
                else { ?>
<input type="text" id="<?= $count ?>_val" name="<?= $count ?>_val[]" value="<?= htmlspecialchars($val,ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?>" class="form-control">
<?php           }
            }
        }
	else { ?>
<input type="text" id="<?= $count ?>_val" name="<?= $count ?>_val[]" value="" class="form-control" required="true">
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
<?php   if ( ! empty($data['object'][$attr]) ) {
          foreach ( $data['object'][$attr] as $val ) {
            if ( $attr == 'businessCategory' ) { ?>
<select id="<?= $count ?>_val" name="<?= $count ?>_val[]">
  <option value="">Select a category</option>
  <option value="Staff"<?= $val == 'Staff' ? " selected":"" ?>>Staff</option>
  <option value="Student"<?= $val == 'Student' ? " selected":"" ?>>Student</option>
  <option value="Guest"<?= $val == 'Guest' ? " selected":"" ?>>Guest</option>
  <option value="Trusted"<?= $val == 'Trusted' ? " selected":"" ?>>Trusted</option>
  <option value="Other"<?= $val == 'Other' ? " selected":"" ?>>Other</option>
  <option value="Confinement"<?= $val == 'Confinement' ? " selected":"" ?>>Confinement</option>
  <option value="Banned"<?= $val == 'Banned' ? " selected":"" ?>>Banned</option>
</select>
<?php       } else {
              if ( strlen($val) > 80 ) { ?>
<textarea id="<?= $count ?>_val" name="<?= $count ?>_val[]" class="form-control"><?= htmlspecialchars($val,ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?></textarea>
<?php         }
              else { ?>
<input type="text" id="<?= $count ?>_val" name="<?= $count ?>_val[]" value="<?= htmlspecialchars($val,ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?>" class="form-control">
<?php         }
            }
          }
        }
        else {
          if ( $attr == 'businessCategory' ) { ?>
<select id="<?= $count ?>_val" name="<?= $count ?>_val[]">
  <option value="">Select a category</option>
  <option value="Staff"<?= $val == 'Staff' ? " selected":"" ?>>Staff</option>
  <option value="Student"<?= $val == 'Student' ? " selected":"" ?>>Student</option>
  <option value="Guest"<?= $val == 'Guest' ? " selected":"" ?>>Guest</option>
  <option value="Trusted"<?= $val == 'Trusted' ? " selected":"" ?>>Trusted</option>
  <option value="Other"<?= $val == 'Other' ? " selected":"" ?>>Other</option>
  <option value="Confinement"<?= $val == 'Confinement' ? " selected":"" ?>>Confinement</option>
  <option value="Banned"<?= $val == 'Banned' ? " selected":"" ?>>Banned</option>
</select>
<?php     } else { ?>
<input type="text" id="<?= $count ?>_val" name="<?= $count ?>_val[]" value="" class="form-control">
<?php     }
        }
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

<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-manage');
    $(el).addClass('active');
  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
