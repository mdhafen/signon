<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
<div class="container">
<h1>WCSD Password Generator</h1>
<div class="mainpage">

<div class="panel panel-default panel-body">
    <p><input id="password" name="password" value="<?= $data['password'] ?>" type="text" class="form-control" onkeyup="CheckEntropy(this.value,'EntropyMeter')"></p>
    <button type="button" class="btn btn-primary" onclick="GeneratePassword('password','EntropyMeter')">Generate Password</button>
</div>

<div class="panel panel-default panel-body">
    <div id="entropy_alert" class="alert alert-danger">Your password does <span id="password_doesnt">not </span>meet WCSD Technology guidelines<br>Strength: <span id="EntropyMeter"></span></div>
</div>

</div>
</div>

<script>
  $(document).ready(function(){
    var el = document.getElementById('password');
    $(el).trigger('keyup');
    el = document.getElementById('nav-passwords');
    $(el).addClass('active');
  });

  function GeneratePassword(input_id,report_id) {
    var el = document.getElementById(report_id);
    while ( el.firstChild ) { el.removeChild(el.firstChild); }
    $(el).append( $("<div class='glyphicon glyphicon-hourglass' title='generating...'>") );
    $.post('<?= $data['_config']['base_url'] ?>api/generate_password.php', {}, function(xml_result) { stuff_xml_password(xml_result,input_id) }, "xml" );
  }

  function stuff_xml_password(xml_result,input_id) {
    var xml_doc = xml_result;
    passwd = $(xml_doc).find('password').text();
    input_obj = document.getElementById(input_id);
    input_obj.value = passwd;
    $(input_obj).trigger('keyup');
  }

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
<?php include( 'doc-close.php' ); ?>
