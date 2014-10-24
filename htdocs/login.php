<?php include( 'doc-open.php' ); ?>
<title>Staff Login Management - Login</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>

<div class="for_login">
<form class="for_login" method="post" action="https://development.washk12.org<?= $_SERVER['REQUEST_URI'] ?>">
  <div id="login">
  <h1>Please Login</h1>
  <span><label for="_username">Username: </label><input id="_username" name="_username" value="<?= isset( $_SESSION['username'] )? $_SESSION[ 'username' ] : "" ?>" /></span><br>
  <span><label for="_password">Password: </label><input id="_password" name="_password" value="" type="password" /></span><br>
<input type="submit" name="button" value="login"/>
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
