<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
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
        <label for="username" class="control-label col-sm-4">Email Address: </label>
        <div class="col-sm-8">
          <input id="username" name="username" value="" class="form-control">
        </div>
      </div>
      <div class="row form-group">
        <label for="oldpassword" class="control-label col-sm-4">Old Password: </label>
        <div class="col-sm-8">
          <input id="oldpassword" name="oldpassword" value="" type="password" class="form-control">
          <div class="help-block">If you are using Google 2-step verification you will need to generate an application-specific password and enter that password in this field to continue.</div>
        </div>
      </div>
      <div class="row form-group">
        <label for="newpassword" class="control-label col-sm-4">New Password: </label>
        <div class="col-sm-8">
          <input id="newpassword" name="password" value="" type="password" class="form-control">
        </div>
      </div>
      <div class="row form-group">
        <label for="verifypassword" class="control-label col-sm-4">Verify Password: </label>
        <div class="col-sm-8">
          <input id="verifypassword" name="password2" value="" type="password" class="form-control">
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default panel-body">
    <input class="btn btn-primary" type="submit" value="Change Password">
  </div>
</form>

<?php } ?>
</div>
<?php include( 'doc-close.php' ); ?>