<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">WCSDsignon Authentication System</a>
        </div>
        <div class="btn-group navbar-right">
<?php if ( ! empty($data['_session']['username']) ) { ?>
          <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
          <span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp&nbsp<?= $data['_session']['username'] ?>
          </a>
            <ul class="dropdown-menu">
              <li><a href="#">Profile</a></li>
              <li class="divider"></li>
              <li><a href="<?= $data['_config']['base_url'] ?>?_logout=1">Sign Out</a></li>
            </ul>
<?php } else { ?>
          <a class="btn" href="<?= $data['_config']['base_url'] ?>admin/">Sign In</a><!-- later link to profile -->
<?php } ?>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
         <ul class="nav navbar-nav">
          <li class="active"><a href="<?= $data['_config']['base_url'] ?>">Home</a></li>
<?php if ( ! empty($data['_session']['CAN_manage_objects']) ) { ?>
          <li><a href="<?= $data['_config']['base_url'] ?>admin/">Manage Users</a></li>
<?php } ?>

         </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

