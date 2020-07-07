<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="modal hidden" id="generic-modal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="generic-modal-title"></h4>
      </div>
      <div class="modal-body" id="generic-modal-message"></div>
    </div>
  </div>
</div>

<div class="container">
<h1>Create account</h1>
<div class="mainpage">

<?php
if ( !empty($data['result']) ) {
  if ( empty($data['error']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Your account has been created.  Please switch to the WCSDaccess network and use your cell phone number, exactly as you entered it on the registration page, to login.  Your password will be sent you by in an SMS.
</div>
<?php
  } else { /* error */ ?>
<div class="alert alert-danger" role="alert">
There was an error!
<?php
    global $Twilio_From;
    switch ($data['result']) {
      case 'blacklist':
        print "You have blocked us.  Please text UNSTOP to $Twilio_From and register again to recieve your password.";
        break;
      default:
        print $data['result'];
    }
?>
</div>
<?php
  }
} else { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  Please enter your information in the form below to register for network access.  You password will then be sent to you by SMS.
</div>

<div class="panel panel-default panel-body">
  <div class="container-fluid">
    <form action="create-guest.php" id="create_guest_form" method="post" class="form-horizontal">

    <div class="row form-group">
      <label for="firstName" class="col-sm-4 control-label">First Name: </label>
      <div class="col-sm-8"><input id="firstName" name="firstName" required="required" value="" class="form-control"></div>
    </div>

    <div class="row form-group">
      <label for="lastName" class="col-sm-4 control-label">Last Name: </label>
      <div class="col-sm-8"><input id="lastName" name="lastName" required="required" value="" class="form-control"></div>
    </div>

    <div class="row form-group">
      <label for="mobile" class="col-sm-4 control-label">Cell Phone Number: </label>
      <div class="col-sm-8"><input id="mobile" name="mobile" value="" class="form-control" type="text" required="required" placeholder="xxx-xxx-xxxx" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" data-toggle="tooltip" title="A cell phone number with the three digit area code, three digit prefix, and four digits, seperated by dashes."></div>
    </div>

    <div class="row form-group">
      <label for="email" class="col-sm-4 control-label">Email: </label>
      <div class="col-sm-8"><input id="email" name="email" value="" class="form-control" type="email"></div>
    </div>

    <div class="row form-group">
      <label for="street" class="col-sm-4 control-label">Address: </label>
      <div class="col-sm-8"><input id="street" name="street" value="" class="form-control"></div>
    </div>

    <div class="row form-group">
      <label for="city" class="col-sm-4 control-label">City: </label>
      <div class="col-sm-8"><input id="city" name="city" value="" class="form-control"></div>
    </div>

    <div class="row form-group">
      <label for="state" class="col-sm-4 control-label">State: </label>
      <div class="col-sm-8"><input id="state" name="state" value="" class="form-control"></div>
    </div>

    <div class="row form-group">
      <label for="zip" class="col-sm-4 control-label">Zip Code: </label>
      <div class="col-sm-8"><input id="zip" name="zip" value="" class="form-control"></div>
    </div>

    <div class="row text-left">
<?php include( $data['_config']['base_dir'] .'/view/create-guest/guest-tos.php' ); ?>
    </div>

    <div class="row form-group">
      <input type="hidden" name="op" value="<?= $data['op'] ?>">
      <button class="btn btn-primary hidden" type="button" name="captcha_submit" id="create_guest_recaptcha_check" onclick="check_recaptcha()">I accept this agreement</button>
      <input class="btn btn-primary hidden" type="submit" name="guest_submit" id="create_guest_submit" value="I accept this agreement">

      <div>
        <img style="display:inline-block; padding-right: 5px" id="captcha_image" src="<?= $data['_config']['base_url'] ?>securimage/securimage_show.php?<?= md5(uniqid(time())) ?>" alt="CAPTCHA Image">
        <div style="display:inline-block">
          <div id="captcha_image_audio_div">
            <audio id="captcha_image_audio" preload="none" style="display: none">
              <source id="captcha_image_source_wav" src="<?= $data['_config']['base_url'] ?>securimage/securimage_play.php?id=1234" type="audio/wav">
            </audio>
          </div>
          <div id="captcha_image_audio_controls">
            <a tabindex="-1" class="captcha_play_button" href="<?= $data['_config']['base_url'] ?>securimage/securimage_play.php?id=1234 ?>" onclick="return false">
              <img class="captcha_play_image" height="32" width="32" src="<?= $data['_config']['base_url'] ?>securimage/images/audio_icon.png" alt="Play CAPTCHA Audio" style="border: 0px">
              <img class="captcha_loading_image rotating" height="32" width="32" src="<?= $data['_config']['base_url'] ?>securimage/images/loading.png" alt="Loading audio" style="display: none">
            </a>
            <noscript>Enable Javascript for audio controls</noscript>
          </div>
          <a tabindex="-1" style="border: 0" href="#" title="Refresh Image" onclick="document.getElementById('captcha_image').src = '<?= $data['_config']['base_url'] ?>securimage/securimage_show.php?' + Math.random(); captcha_image_audioObj.refresh(); this.blur(); return false">
            <img height="32" width="32" src="<?= $data['_config']['base_url'] ?>securimage/images/refresh.png" alt="Refresh Image" onclick="this.blur()" style="border: 0px; vertical-align: bottom" />
          </a>
        </div>
        <script type="text/javascript" src="<?= $data['_config']['base_url'] ?>securimage/securimage.js"></script>
        <script type="text/javascript">
          captcha_image_audioObj = new SecurimageAudio({ audioElement: 'captcha_image_audio', controlsElement: 'captcha_image_audio_controls' });
        </script>
        <div style="clear: both"></div>
        <p>
          <label for="captcha_code">Type the text:</label>
          <input type="text" name="captcha_code" id="captcha_code">
        </p>
        <button class="btn btn-primary" type="button" name="captcha_submit" id="create_guest_sicaptcha_check" onclick="verify_sicaptcha()">I accept this agreement</button>
      </div>

    </div>
    </form>
  </div>
</div>
<?php } ?>

</div>
</div>
<!-- <script src="https://www.recaptcha.net/recaptcha/api.js?render=<?= $data['recaptcha_key'] ?>"></script> -->
<script>
  function check_recaptcha() {
    grecaptcha.ready(function(){
      grecaptcha.execute('<?= $data['recaptcha_key'] ?>', {action: 'createguest'}).then(function(token) {
        var data = {
          'g-recaptcha-response': token,
          'op': 'recaptcha-verify'
        };
        $.post('<?= $data['_config']['base_url'] ?>api/recaptcha_verify.php', data, function(result){
          var status = $(result).find('state').text();
          if ( status == 'success' ) {
              document.getElementById('create_guest_submit').click();
          }
          else {
            $('#generic-modal #generic-modal-title').empty().text('Error');
            $('#generic-modal #generic-modal-message').empty().text( $(result).find('message').text() );
            $('#generic-modal').modal('show');
          }
        });
      });
    });
  }

  function verify_sicaptcha() {
    var token = $("#captcha_code").val();
    var data = {
      'captcha_code': token,
      'op': 'sicaptcha-verify'
    };
    $.post('<?= $data['_config']['base_url'] ?>api/recaptcha_verify.php', data, function(result){
      var status = $(result).find('state').text();
      if ( status == 'success' ) {
          document.getElementById('create_guest_submit').click();
      }
      else {
        $('#generic-modal #generic-modal-title').empty().text('Error');
        $('#generic-modal #generic-modal-message').empty().text( $(result).find('message').text() );
        $('#generic-modal').modal('show');
      }
    });
  }

  $(document).ready(function(){
    var el = document.getElementById('nav-home');
    $(el).addClass('active');
    $('#generic-modal').modal({'show':false});
  });
  $('[data-toggle="tooltip"]').tooltip({trigger:'focus'});

  $("#create_guest_form").submit(function (e) {
    // Check if we have submitted before
    if ( $("#create_guest_submit").attr('data-attempted') == 'true' ) {
      //stop submitting the form because we have already clicked submit.
      e.preventDefault();
    }
    else {
      $("#create_guest_submit").attr("data-attempted", 'true');
    }
  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
