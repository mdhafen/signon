<?php include( $data['_config']['base_dir'] .'/htdocs/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<h1>Create account</h1>
<div class="mainpage">

<?php
if ( !empty($data['result']) ) {
  if ( empty($data['error']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
Your account has been created.  Please switch to the WCSDaccess network and use your phone number, exactly as you entered it on the registration page, to login.  Your password will be sent you by in an SMS.
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
    <form action="create.php" method="post" class="form-horizontal">
    <div class="row form-group">
      <label for="mobile" class="col-sm-4 control-label">Phone Number: </label>
      <div class="col-sm-8"><input id="mobile" name="mobile" value="" class="form-control" type="tel" required="required" placeholder="xxx-xxx-xxxx" pattern="[0-9]{0,3}-?[0-9]{3}-[0-9]{4}" title="A phone number with an optional three digit area code and dash, three digit prefix and dash, and four digits"></div>
    </div>

    <div class="row form-group">
      <label for="firstName" class="col-sm-4 control-label">First Name: </label>
      <div class="col-sm-8"><input id="firstName" name="firstName" value="" class="form-control"></div>
    </div>

    <div class="row form-group">
      <label for="lastName" class="col-sm-4 control-label">Last Name: </label>
      <div class="col-sm-8"><input id="lastName" name="lastName" value="" class="form-control"></div>
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

    <div class="row form-group">
      <input type="hidden" name="op" value="<?= $data['op'] ?>">
      <input class="btn btn-primary" type="submit" name="submit" value="Register">
    </div>
    </form>
  </div>
</div>
<?php } ?>

</div>
<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>