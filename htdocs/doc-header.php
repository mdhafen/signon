<body>
    <nav class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?= $data['_config']['base_url'] ?>">WCSDsignon System</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
         <ul class="nav navbar-nav">
          <li class="active"><a href="<?= $data['_config']['base_url'] ?>">Home</a></li>
<?php if ( ! empty($data['_session']['CAN_reset_password']) ) { ?>
          <li><a href="<?= $data['_config']['base_url'] ?>admin/search.php">Search</a></li>
          <li><a href="<?= $data['_config']['base_url'] ?>admin/">Manage Users</a></li>
<?php } ?>

         </ul>
<?php if ( ! empty($data['_session']['username']) ) { ?>
         <div class="nav navbar-right dropdown">
          <a class="navbar-btn btn dropdown-toggle" data-toggle="dropdown" href="#">
          <span class="glyphicon glyphicon-user"> <?= $data['_session']['username'] ?></span>
          </a>
          <ul class="dropdown-menu">
            <li><a href="<?= $data['_config']['base_url'] ?>profile.php">Profile</a></li>
            <li class="divider"></li>
            <li><a href="<?= $data['_config']['base_url'] ?>?_logout=1">Sign Out</a></li>
          </ul>
<?php } else { ?>
	 <div class="nav navbar-right">
           <a class="navbar-btn btn" href="<?= $data['_config']['base_url'] ?>profile.php">Sign In</a>
<?php } ?>
         </div>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>
