<?php include( 'doc-open.php' ); ?>
<title>Welcome to the Staff Management page</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
include( 'doc-menu.php' );
?>

<h1>Welcome to the Staff Management page</h1>

<p>
Still under construction, nothing to see here.
</p>

<h2>For debugging...</h2>
<p class="prewrap">
<?= !empty($data['user']['dn']) ? $data['user']['dn'] : "" ?><br>
<?= !empty($data['next']) ? $data['next'] : "" ?><br>
<?= var_dump($data['user']) ?><br>
</p>

<?php include( 'doc-close.php' ); ?>
