<?php include( 'doc-open.php' ); ?>
<title>Staff Login Management - Error</title>
<?php
include( 'doc-head-close.php' );
include( 'doc-header.php' );
?>

<div class="container">
<h1>An error occured!</h1>

<?php
foreach ( $data['errors'] as $error ) {
?>
<div class='important'><?= $error ?></div>
<?php
}
?>
</div>
<?php include( 'doc-close.php' ); ?>
