<?php include( 'doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?></title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>
<div class="jumbotron">
      <div class="container">
  <center>
  <img src='img/crest.png' height='200px'>
  </center>
      </div>
</div>

<div class="container">
<div class="mainpage">
<h2>Need an WCSDsignon account?</h2>
<div class="row">
<div class="col-md-4">
<a href="create/create.php?op=Student" class="btn btn-primary btn-lg btn-block"><small>I am a</small><br>Student</a>
</div>
<div class="col-md-4">
<a href="create/create.php?op=Employee" class="btn btn-primary btn-lg btn-block"><small>I am a</small><br>WCSD Employee</a>
</div>
<div class="col-md-4">
<a href="create-guest/create-guest.php?op=Guest" class="btn btn-primary btn-lg btn-block"><small>I am a</small><br>Visitor</a>
</div>
</div>
<br>
<img src='img/WCSDsignon.jpg'>

</div>
</div><!-- /container -->
<?php include( 'doc-close.php' ); ?>