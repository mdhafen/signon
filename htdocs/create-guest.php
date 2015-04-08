<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>

<h1>Create account</h1>
<div class="mainpage">

<?php
if ( !empty($data['result']) ) {
  if ( empty($data['error']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Your account has been created.  Please switch to the WCSDaccess network and use your phone number and password to login.
</div>
<?php
  } else { /* error */ ?>
<div class="alert alert-danger" role="alert">
There was an error! <?= $data['error'] ?>
</div>
<?php
  }
} else { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  Please enter the form below to register for network access.
</div>

<div class="panel panel-default panel-body">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-4 text-right">Phone Number: </div>
      <div class="col-sm-8 text-left"><?= $data['object']['uid'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">First Name: </div>
      <div class="col-sm-8 text-left"><?= $data['object']['givenName'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">Last Name: </div>
      <div class="col-sm-8 text-left"><?= $data['object']['sn'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">E-Mail: </div>
      <div class="col-sm-8 text-left"><?= $data['object']['mail'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">Address: </div>
      <div class="col-sm-8 text-left"><?= $data['object']['street'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">City: </div>
      <div class="col-sm-8 text-left"><?= $data['object']['l'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">State: </div>
      <div class="col-sm-8 text-left"><?= $data['object']['st'][0] ?></div>
    </div>
    <div class="row">
      <div class="col-sm-4 text-right">Zip/Postal Code: </div>
      <div class="col-sm-8 text-left"><?= $data['object']['postalCode'][0] ?></div>
    </div>
  </div>
</div>

</div>
<?php include( 'doc-close.php' ); ?>
