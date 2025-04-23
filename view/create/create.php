<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<h1>Create account / Change password</h1>
<div class="mainpage">
<?php
if ( !empty($data['result']) ) {
  if ( empty($data['error']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Your account has been updated / created.  You can now access the WCSDaccess wireless network using your district email address and password to login.
</div>
<?php
  } else { /* error */ ?>
<div class="alert alert-danger" role="alert">
There was an error! <?= $data['result'] ?>
</div>
<?php
  }
} else { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<?php if ( !empty($data['username']) ) { ?>
  <h2>Welcome <?= $data['username'] ?></h2>
<?php } ?>
  Please enter your new Washington County School District Email password below.
</div>

<div class="panel panel-default panel-body">
  <div class="container-fluid">
    <form action="create.php" method="post" class="form-horizontal">
    <div class="row form-group">
      <label for="password" class="col-sm-4 control-label">Password: </label>
      <div class="col-sm-8">
        <input id="password" name="password" value="" type="password" class="form-control">
	<div class="help-block">The password you use to login to your email will also be the same password that is used for the WCSDsignon System</div>
      </div>
    </div>
    <div class="row form-group">
      <label for="password2" class="col-sm-4 control-label">Password Again: </label>
      <div class="col-sm-8">
        <input id="password2" name="password2" value="" type="password" class="form-control">
      </div>
    </div>
    <div class="row form-group">
      <input type="hidden" name="op" value="<?= $data['op'] ?>">
      <input class="btn btn-primary" type="submit" name="submit" value="Submit">
    </div>
    </form>
  </div>
</div>
<?php } ?>

</div>
</div>
<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-home');
    $(el).addClass('active');
  });
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
