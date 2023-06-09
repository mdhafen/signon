<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Login</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>

<div class="container">
<form class="form-signin form-horizontal" method="post" action="<?= substr($data['_config']['base_url'],0,strpos($data['_config']['base_url'],'/',8)) ?><?= $_SERVER['PHP_SELF'] ?>">
  <h2 class='form-signin-heading'>WCSDsignon</h2>
  <div class="form-group">
  <div class="input-group">
  <label for="_username" class="sr-only">Username</label>
  <input type="username" id="_username" name="_username" class="form-control" placeholder="Username" value="<?= isset( $_SESSION['username'] )? $_SESSION[ 'username' ] : "" ?>" required autofocus><span class="input-group-addon">@washk12.org</span>
  </div>
  <label for="_password" class="sr-only">Password</label>
  <input type="password" id="_password" name="_password" class="form-control" placeholder="Password" required>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  </div>
<?php
  if ( isset($_SESSION['NOSESSION']) || isset($_SESSION['BADLOGIN']) || isset($_SESSION['NOTPERMITTED']) ) {
    echo "<div class='important'>\n";
    if ( isset($_SESSION[ 'NOSESSION' ]) ) {
      echo "<!-- Not Logged In -->\n";
    }
    if ( isset($_SESSION[ 'BADLOGIN' ]) ) {
      echo "<span>Incorrect Login</span><br>\n";
    }
    if ( isset($_SESSION[ 'NOTPERMITTED' ]) ) {
      echo "<span>Not authorized</span><br>\n";
    }
    echo "</div>\n";
}
?>
</form>
</div>

<?php include( 'doc-close.php' ); ?>
