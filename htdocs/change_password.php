<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
<div class="container">
<div class="mainpage">
<?php if ( !empty($data['success']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  Your password has been changed.
</div>
<?php } else { ?>
<h1 class="modal-title" id="password_modal_label">Change Password</h1>
<form action="change_password.php" method="post" class="form-horizontal">
  <div class="panel panel-default panel-body">
    <div class="container-fluid">
      <div class="row form-group">
        <label for="newpassword" class="control-label col-sm-4">New Password: </label>
        <div class="col-sm-8">
          <input id="newpassword" name="password" value="" type="password" class="form-control" onkeyup="CheckEntropy(this.value,'EntropyMeter')">
        </div>
      </div>
      <div class="row form-group">
        <div id="entropy_alert" class="alert alert-danger">Your password does <span id="password_doesnt">not </span>meet WCSD Technology guidelines<br>Strength: <span id="EntropyMeter"></span></div>
      </div>
      <div class="row form-group">
        <label for="verifypassword" class="control-label col-sm-4">Verify Password: </label>
        <div class="col-sm-8">
          <input id="verifypassword" name="password2" value="" type="password" class="form-control">
        </div>
      </div>
    </div>
    <div>
      Washington County School District recommends passwords have an entropy value of at least 70.
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
  </div>
  <div class="panel panel-default panel-body">
    <input class="btn btn-primary" type="submit" value="Change Password">
  </div>
</form>

<script>
  Math.log2 = Math.log2 || function(x){ Math.log(x)*Math.LOG2E; };
  function CheckEntropy(value,report_el_id) {
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
<?php } ?>
</div>
</div>
<?php include( 'doc-close.php' ); ?>
