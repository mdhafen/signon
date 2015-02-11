<?php include( 'doc-open.php' ); ?>
<title>Staff Login Management - Login</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>

<div class="container">
<form class="form-signin" method="post" action="https://development.washk12.org<?= $_SERVER['REQUEST_URI'] ?>">
<div class="row">
  <div class="col-md-4"></div><!--YES ITS UGLY, plan to fix -->
<div class="col-md-4">
  <h2 class='form-signin-heading'>WCSDsomething?????</h2>
  <label for="_username" class="sr-only">Username</label>
  <input type="username" id="_username" name="_username" class="form-control" placeholder="Username" value="<?= isset( $_SESSION['username'] )? $_SESSION[ 'username' ] : "" ?>" required autofocus>
  <label for="_password" class="sr-only">Password</label>
  <input type="password" id="_password" name="_password" class="form-control" placeholder="Password" required>
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
 </div>
<div class="col-md-4"></div>
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
