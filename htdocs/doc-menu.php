<div id="nav_anchor">
<menu id="nav">
<li id="nav_first">Main Menu</li>
<li><a href="<?= $data['_config']['base_url'] ?>">Home</a></li>
<?php if ( ! empty($data['_session']['CAN_change_security_group']) ) { ?>
<li>Site Management
 <menu>
  <li><a href="<?= $data['_config']['base_url'] ?>manage/users.php">Users</a></li>
 </menu>
</li>
<?php } ?>
<?php if ( $data['_session']['username'] ) { ?>
<li><a href="<?= $data['_config']['base_url'] ?>?_logout=1">Logout</a></li>
<?php } ?>
</menu>
</div>
