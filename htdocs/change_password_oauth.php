<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<mega name="google-signin-client_id" content="WCSDAccess.apps.googleusercontent.com">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="https://apis.google.com/js/api:client.js"></script>
<script>
  var googleUser = {};
  var startApp = function() {
    gapi.load('auth2', function() {
      auth2 = gapi.auth2.init({
        client_id: "WCSDAccess.apps.googleusercontent.com",
        cookiepolicy: 'single_host_origin'
      });
      auth2.attachClickHandler( 'theButton', {}, function(googleUser) { OnSignIn(googleUser) } );
    });
  };

  function OnSignIn(googleUser) {
    var profile = googleUser.getBasicProfile();
    var currentUseremail = profile.getEmail();
    var domain = currentUserEmail.substring( currentUserEmail.lastIndexOf("@") + 1 );
    if ( domain == 'washk12.org' ) {
      var id_token = googleUser.getAuthResponse().id_token;
      document.form.oAuth.token.value = id_token;
      document.form.oAuth.submit();
    }
  }
</script>
<style type="text/css">
  #theButton {
    display: none;
  }
</style>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
<div class="mainpage">
<form action="change_password.php" id="oAuth" method="post" class="form-horizontal">
  <input id="token" value="" type="hidden">
  <input id="theButton" type="button" value="Google Sign In">
</form>

<?php } ?>
</div>
<script>
  startApp();
  document.form.oAuth.theButton.click();
</script>
<?php include( 'doc-close.php' ); ?>
