<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
<!-- Change Password Modal -->
<div class="modal fade" id="password_modal" role="dialog" aria-labelledby="password_modal_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="profile.php" method="post" class="form-horizontal">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="password_modal_label">Change Password</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
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
        <div class="modal-footer">
          <input class="btn btn-primary" type="submit" value="Change Password">
          <a href="#" class="btn btn-default" data-dismiss="modal">Cancel</a>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Link to EmployeeOnline Modal -->
<div class="modal fade" id="linkeo_modal" role="dialog" aria-labelledby="linkeo_modal_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="profile.php" method="post" class="form-horizontal">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="linkeo_modal_label">Link to EmployeeOnline</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row form-group">
            <label for="employeeid" class="control-label col-sm-6">employeeOnline EmployeeID: </label>
            <div class="col-sm-6">
              <input id="employeeid" name="employeeNumber" value="" class="form-control">
            </div>
          </div>

          <div class="row form-group">
            <label for="employeessn" class="control-label col-sm-6">Social Security Number: </label>
            <div class="col-sm-6">
              <input id="employeessn" name="SSN" value="" class="form-control">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <input class="btn btn-primary" type="submit" value="Link to EmployeeOnline">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>

<h1><?= $data['object']['cn'][0] ?></h1>
<div class="mainpage">

<div class="panel panel-default panel-body">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-4 text-right">First Name: </div>
      <div class="col-sm-8 text-left"><?= empty($data['object']['givenName'][0]) ? $data['object']['cn'][0] : $data['object']['givenName'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">Last Name: </div>
      <div class="col-sm-8 text-left"><?= empty($data['object']['sn'][0]) ? "" : $data['object']['sn'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">Username: </div>
      <div class="col-sm-8 text-left"><?= empty($data['object']['uid'][0]) ? "" : $data['object']['uid'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">E-Mail: </div>
      <div class="col-sm-8 text-left"><?= empty($data['object']['mail'][0]) ? "" : $data['object']['mail'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">Location: </div>
      <div class="col-sm-8 text-left"><?= empty($data['object']['l'][0]) ? "" : $data['object']['l'][0] ?></div>
    </div>
  </div>
</div>

<div class="panel panel-default panel-body">
  <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#password_modal">Change Password</button>
  <?php if ( !empty($data['object']['employeeType'][0]) && $data['object']['employeeType'][0] == 'Staff' ) { ?>
<!--  <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#linkeo_modal">Link to Employee Online</button> -->
<?php } ?>
</div>

</div>
<?php include( 'doc-close.php' ); ?>
