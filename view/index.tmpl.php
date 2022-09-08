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
<a href="create/create.php" class="btn btn-primary btn-lg">WCSD Employee / Student Registration</a>
</div>
<br>
<img src='img/WCSDsignon.jpg'>

</div>

<!-- <a class="btn btn-default" role="button" href="<?= $data['_config']['base_url'] ?>tutorials.php" aria-expanded="false" aria-controls="tutorial_tabs">View our Tutorials</a> -->

</div><!-- /container -->

</div>
<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-home');
    $(el).addClass('active');
  });
</script>
<?php include( 'doc-close.php' ); ?>
