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
Your account has been created.  Please switch to the WCSDaccess network and use your email <span data-toggle="tooltip" title="Your usename is the part of your email address before the '@'.">username</span> and password to login.
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
  Please enter your Washington County School District EMail address and password below.
</div>

<div class="panel panel-default panel-body">
  <div class="container-fluid">
    <form action="create.php" method="post" class="form-horizontal">
    <div class="row form-group">
      <label for="email" class="col-sm-4 control-label">E-Mail: </label>
      <div class="col-sm-8">
        <input id="email" name="email" value="" class="form-control">
      </div>
    </div>
    <div class="row form-group">
      <label for="password" class="col-sm-4 control-label">Password: </label>
      <div class="col-sm-8">
        <input id="password" name="password" value="" type="password" class="form-control">
      </div>
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
<?php include( 'doc-close.php' ); ?>
