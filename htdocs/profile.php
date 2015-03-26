<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
<h1><?= $data['object']['cn'][0] ?></h1>
<div class="container-fluid">
<div class="mainpage row">

<div class="col-sm-6 panel panel-default">
<div class="row panel-body">
<form action="profile.php" method="post" class="form-horizontal">
  <div class="form-group">
    <label for="newpassword" class="control-label col-sm-4">New Password: </label>
    <div class="col-sm-8">
      <input id="newpassword" name="password" value="" type="password" class="form-control">
    </div>
  </div>
  <div class="form-group">
    <label for="verifypassword" class="control-label col-sm-4">Verify Password: </label>
    <div class="col-sm-8">
      <input id="verifypassword" name="password2" value="" type="password" class="form-control">
    </div>
  </div>
  <input class="btn btn-primary" type="submit" value="Reset Password">
</form>
</div>
<br>
</div>
<div class="col-sm-6 panel panel-default">
<div class="row panel-body">
<form action="profile.php" method="post" class="form-horizontal">
  <div class="form-group">
    <label for="employeeid" class="control-label col-sm-6">employeeOnline EmployeeID: </label>
    <div class="col-sm-6">
      <input id="employeeid" name="employeeNumber" value="<?= $data['object']['employeeNumber'][0] ?>" class="form-control">
    </div>
  </div>
  <input class="btn btn-primary" type="submit" value="Link to EmployeeOnline">
</form>
</div>
</div>

</div>
</div>
<?php include( 'doc-close.php' ); ?>
