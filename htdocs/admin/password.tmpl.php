<?php include($data['_config']['base_dir'] .'/htdocs/doc-open.php'); ?>
<title>Staff Login Management - Set Password</title>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-menu.php' );
?>

<h1><?= $data['object_dn'] ?></h1>
<div>
<?php if ( ! empty($data['success']) ) { ?>
<div>Success!</div>
 <?php if ( strcasecmp($data['object_dn'],$data['_session']['userid']) == 0 ) { ?>
<div><a href="<?= $data['_config']['base_url'] ?>?_logout=1">Logout</a></div
 <?php } else { ?>
<div><a href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Back</a></div
 <?php } ?>
<?php } else if ( ! empty($data['error']) ) { ?>
<?= $data['error'] ?>
<?php } else { ?>
<form method="post" action="password.php">
<div>
<label for="password">Password</label>
<input type="password" id="password" name="password">
</div>
<div>
<label for="confirm">Repeat Password</label>
<input type="password" id="confirm" name="confirm">
</div>
<div>
<input type="submit" value="Set">
<input type="hidden" name="dn" value="<?= htmlspecialchars($data['object_dn']) ?>">
<a href="object.php?dn=<?= urlencode($data['object_dn']) ?>">Cancel</a>
</div>
</form>
<?php } ?>
</div>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
