<?php include($data['_config']['base_dir'] .'/view/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Set Password</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<h2>Set WCSDaccess and Google Password</h2>

<?php if ( ! empty($data['success']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Success!
 <?php if ( strcasecmp($data['object_dn'],$data['_session']['userid']) == 0 ) { ?>
<a class="btn" href="<?= $data['_config']['base_url'] ?>?_logout=1">Logout</a>
 <?php } else { ?>
<a class="btn" href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Back</a>
 <?php } ?>
</div>
<?php } else if ( ! empty($data['error']) ) { ?>
<div class="alert alert-danger" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<?= $data['error'] ?>
<?php if ( $data['error'] == 'PASS_TO_COMMON' ) { ?>
<p><span class="big">Password compromised, you can not use this password.</span><br>  This password has been seen <?= $data['error_times'] ?> times before.  <span class="small">This password has previously appeared in a data breach and should never be used.  If you've ever used it anywhere before, you should change it as soon as possible.</span></p>
<?php } ?>
</div>
<?php } ?>
<div class="panel panel-default panel-body">
<div class="container-fluid">
<form method="post" action="password.php" class="form-horizontal">

<div class="row form-group">
<label for="password" class="col-sm-4 control-label">Password</label>
<div class="col-sm-8"><input type="password" id="password" name="password" class="form-control" onkeyup="CheckEntropy(this.value,'EntropyMeter')"></div>
</div>

<div class="row form-group">
<label for="confirm" class="col-sm-4 control-label">Repeat Password</label>
<div class="col-sm-8"><input type="password" id="confirm" name="confirm" class="form-control"></div>
</div>

<div class="row form-group">
  <div id="entropy_alert" class="alert alert-danger">Your password does <span id="password_doesnt">not </span>meet WCSD Technology guidelines<br>Strength: <span id="EntropyMeter"></span></div>
</div>

<div class="row form-group">
<input type="submit" value="Set" class="btn">
<input type="hidden" name="dn" value="<?= htmlspecialchars($data['object_dn']) ?>">
<a class="btn" href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Cancel</a>
</div>
</div>
</form>
<div>
     Washington County School District recommends passwords have an entropy value (strength) of at least 70.
</div>
<div>
  This password will be used on the following services:
  <ul>
    <li>Washk12 Mail</li>
    <li>PowerSchool</li>
    <li>WCSDAccess Wireless</li>
    <li>OverDrive</li>
  </ul>
</div>
<script>
  Math.log2 = Math.log2 || function(x){ Math.log(x)*Math.LOG2E; };
  function CheckEntropy(value,report_el_id) {
    $("#entropy_alert").show();
    var guideline = 70;
    var char_sets = {
      lower: { reg: /[a-z]/g, entropy:26 },
      upper: { reg: /[A-Z]/g, entropy:26 },
      numbers: { reg: /[0-9]/g, entropy:10 },
      punct: { reg: /[,\.\?'";:!@#\$%\^&\*\-_]/g, entropy:17 }, // '
      symbols: { reg: /[`~\(\)+=\{\}\|\[\]\\/<>]/g, entropy:15 } // `
    };
    var el = document.getElementById(report_el_id);
    var length = value.length;
    var entropy = 0;
    for ( var set in char_sets ) {
        if ( char_sets.hasOwnProperty(set) && char_sets[set].reg.test(value) ) {
            value = value.replace( char_sets[set].reg, "" );
            entropy += char_sets[set].entropy;
        }
    }
    if ( entropy ) { entropy = Math.floor( Math.log2(entropy) ) * length; }
    while ( el.firstChild ) { el.removeChild(el.firstChild); }
    el.appendChild( document.createTextNode(entropy) );
    if ( entropy >= guideline ) {
        $("#entropy_alert").removeClass("alert-danger").addClass("alert-success");
        $("#password_doesnt").hide();
    }
    else {
        $("#entropy_alert").removeClass("alert-success").addClass("alert-danger");
        $("#password_doesnt").show();
    }
  }
</script>

</div>
</div>

<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-manage');
    $(el).addClass('active');
    $("#entropy_alert").hide();
  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
