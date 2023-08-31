<?php include( $data['_config']['base_dir'] .'/view/doc-open.php' ); ?>
<title><?= $data['_config']['site_title'] ?> - Details</title>
<?php
include( $data['_config']['base_dir'] .'/view/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/view/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1><?= $data['object_dn'] ?></h1>
<div class="panel panel-default panel-body">
<?php if ( $data['is_guest'] && $data['is_guest_expired'] ) { ?>
<div class="container-fluid bg-danger">
<p>This is a guest account with an expired Guest AUP signature.  They will not be able to connect to the wireless unless they renew their account (signature).</p>
<p>Click here to <button type="button" class="btn" onclick="SendNotice('<?= htmlspecialchars($data['object']['uid'][0],ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?>')">send the renewal notice</button>.</p>
<div class="bg-success hidden" id="expired_guest_notice_sent"></div>
</div>
<?php } ?>
<div class="container-fluid">
<?php foreach ( $data['object'] as $key => $vals ) { ?>
<?php
    if ( empty($data['can_see_password']) && ! in_array($key,['cn','uid','mail','o']) ) {
      continue;
    }
?>
<div class="row">
<div class="col-sm-4 text-right"><?= $key ?>:</div>
<div class="col-sm-8">
<?php   foreach ( $vals as $val ) { ?>
  <div><?= htmlspecialchars($val,ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE) ?></div>
<?php   } ?>
<?php   if ( !empty($data['attr_changes'][$key]) ) { ?>
  <div class="text-muted">Last changed <?= date('m/d/Y g:i a',strtotime($data['attr_changes'][$key]['timestamp'])) ?> by <?= $data['attr_changes'][$key]['user'] ?> at <?= $data['attr_changes'][$key]['user_ip'] ?></div>
<?php   } ?>
</div>
</div>
<?php } ?>

<div class="row">
<?php if ( $data['parentdn'] ) { ?>
<a class="btn btn-default" href="object.php?dn=<?= urlencode($data['parentdn']) ?>">Up</a>
<?php } else { ?>
<a class="btn btn-default" href="index.php">Up</a>
<?php } ?>

<?php if ( ! empty( $data['can_edit'] ) ) { ?>
<a class="btn btn-default" href="edit.php?dn=<?= urlencode($data['object_dn']) ?>">Edit</a>
<a class="btn btn-warning" href="delete.php?dn=<?= urlencode($data['object_dn']) ?>">Delete</a>
<?php if ( empty($data['is_person']) ) { ?>
Add:
<a class="btn btn-default" href="add.php?class=security&amp;parent=<?= urlencode($data['object_dn']) ?>">Security Account</a>
<a class="btn btn-default" href="add.php?class=user&amp;parent=<?= urlencode($data['object_dn']) ?>">User</a>
<a class="btn btn-default" href="add.php?class=group&amp;parent=<?= urlencode($data['object_dn']) ?>">Group</a>
<a class="btn btn-default" href="add.php?class=folder&amp;parent=<?= urlencode($data['object_dn']) ?>">Folder</a>
<?php }
  } ?>

<div class="form-group">
<h4>Password</h4>
<?php
if ( ! empty($data['can_edit']) && ! empty($data['is_person']) ) {
?>
<?php
    if ( !empty($data['default_passwd']) ) { ?>
<a class="btn btn-default" href="password.php?default=1&amp;dn=<?= urlencode($data['object_dn']) ?>">Set Password to Default</a>
<?php
    }
?>
<a class="btn btn-default" href="password.php?dn=<?= urlencode($data['object_dn']) ?>">Reset Password</a>
<?php } ?>
<?php
    if ( !empty($data['can_send_token']) ) {
?>
<button type="button" class="btn btn-default" id="reset_link_btn" data-toggle="collapse" data-target="#reset_link_details">Password Reset Link</button>
<div class="collapse" id="reset_link_details"><div class="well">
<div>
<label for="reset_verify"><input type="checkbox" name="reset_verify" id="reset_verify"> I have verified the users personal email address, and that they can still access it.</label>
</div>
<div>
  <button onclick="send_password_reset()" type="button" class="btn btn-warning">Send new password reset link email to <?= $data['object']['labeledURI'][0] ?></button>
  <span class="help-block" id="reset_link">
<?php if ( !empty($data['password_reset_token']) ) { ?>
<?= $data['_config']['base_url'] ?>change_password.php?op=Reset&amp;token=<?= htmlentities(rawurlencode($data['password_reset_token']['token'])) ?> (active until <?= date('F j, Y, g:i a',$data['password_reset_token']['expire_timestamp']) ?>)
<?php } ?>
  </span>
</div>
<div id="reset_verify_modal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-content" role="document">
    <div class="modal-body">
      <p class="alert alert-danger" role="alert">Please verify the users personal email address.</p>
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
  </div>
</div> <!-- modal -->
</div></div>
<?php } ?>
<?php if ( !empty($data['can_see_password']) && !empty($data['default_passwd']) ) { ?>
<div class="form-group">
<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#default_passwd_details">Show/Hide Default Password</button>
<div class="collapse" id="default_passwd_details"><div class="well">
Password: <?= $data['default_passwd'] ?><br>
</div></div>
</div>
<?php } ?>
</div>
<?php if ( ! empty($data['can_edit']) && ! empty($data['is_person']) ) { ?>
<div class="form-group">
<h4>Security</h4>
<a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;toggle=<?= ($data['object']['businessCategory'][0] != 'Confinement' && $data['object']['businessCategory'][0] != 'Banned' ? 'on' : 'off') ?>" <?= ($data['object']['businessCategory'][0] != 'Confinement' && $data['object']['businessCategory'][0] != 'Banned' ? 'class="btn btn-success">WiFi Access Enabled' : 'class="btn btn-danger">WiFi Access Disabled' ) ?></a>
<a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;class=Banned&amp;toggle=<?= ($data['object']['businessCategory'][0] != 'Banned' ? 'on' : 'off') ?>" <?= ($data['object']['businessCategory'][0] != 'Banned' ? 'class="btn btn-success">GinaAccess Logins Enabled' : 'class="btn btn-danger">GinaAccess Logins Disabled' ) ?></a>
<?php if ( !empty($data['can_lock']) ) { ?>
<?php if ( empty($data['user_lock']) ) { ?>
      <a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;toggle=on&amp;class=Lock" class="btn btn-success">User is Unlocked</a>
<?php } else { ?>
<a href="../api/confine.php?dn=<?= urlencode($data['object_dn']) ?>&amp;return=1&amp;toggle=off&amp;class=Lock" class="btn btn-danger">User is Locked</a>
<span class="help-block">After unlocking share the lock password with the user.</span>
<?php } } ?>
</div>

<?php
    if ( !empty($data['user_lock']) ) {
?>
<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#lock_details">Show/Hide Lock Details</button>
<div class="collapse" id="lock_details"><div class="well">
Password: <?= $data['user_lock']['passwd'] ?><br>
Locked by: <?= $data['user_lock']['user'] ?><br>
Locked on: <?= $data['user_lock']['timestamp'] ?><br>
</div></div>
<?php } ?>
<?php
}
?>
</div>

</div>
</div><!-- Object Panel -->

<?php if ( count( $data['children'] ) ) { ?>
<h2>Children</h2>
<div class="panel panel-default panel-body">
<?php foreach ( $data['children'] as $child ) { ?>
<p>
<a href="object.php?dn=<?= urlencode($child['dn']) ?>"><?php $rdn_attr = substr( $child['dn'], 0, strpos($child['dn'],'=') ); print $child[$rdn_attr][0]; ?></a> <span class="badge">
<?php if ( in_array('inetOrgPerson',$child['objectClass']) ) { ?>
(Person)
<?php } else if ( in_array('person',$child['objectClass']) || in_array('simpleSecurityObject',$child['objectClass']) ) { ?>
(System Account)
<?php } else if ( in_array('groupOfNames',$child['objectClass']) || in_array('posixGroup',$child['objectClass']) ) { ?>
(Group)
<?php } else if ( in_array('organizationalUnit',$child['objectClass']) ) { ?>
(Folder)
<?php } else { ?>
(Object)
<?php } ?>
</span></p>
<?php } ?>
</div>
<?php } ?>

</div>
</div>

<script>
  $(document).ready(function(){
    var el = document.getElementById('nav-manage');
    $(el).addClass('active');

  });

  function send_password_reset() {
    if ( ! $('#reset_verify').prop('checked') ) {
      $('#reset_verify_modal').modal('show');
      return;
    }
    var data = {'dn':<?= json_encode($data['object_dn']) ?>};
    $.post('<?= $data['_config']['base_url'] ?>api/send_password_reset_token.php', data, function(xml_result) { show_password_reset_link(xml_result) }, "xml" );
  }

  function show_password_reset_link( xml_result ) {
      var el = $('#reset_link');
      var xml_doc = xml_result;
      var state = $(xml_doc).find('state').text();
      el.empty();
      if ( state == 'Success' ) {
        $('#reset_verify').prop('checked', false);
        var token = $(xml_doc).find('token').text();
        var expire = new Date($(xml_doc).find('expire').text());
        el.append(document.createTextNode("<?= $data['_config']['base_url'] ?>change_password.php?op=Reset&uid=<?= rawurlencode( !empty($data['is_person']) ? $data['object']['uid'][0] : '' ) ?>&token="+token));
        el.append(document.createTextNode( " (active until "+ expire.toLocaleString("en-US", {dateStyle:"long",timeStyle:"short"}) +")" ));
      }
      else {
        el.append(document.createTextNode("Error: "+ $(xml_doc).find('message').text()));
      }
  }

  function SendNotice(object_uid) {
    var report_el = document.getElementById('expired_guest_notice_sent');
    $(report_el).removeClass(['show','bg-warning']).addClass(['hidden','bg-success']);
    while ( report_el.firstChild ) { report_el.removeChild(report_el.firstChild); }
    var data = {'uid':object_uid};
    $.post('<?= $data['_config']['base_url'] ?>api/send_renew_notice.php', data, function(xml_result) { show_guest_notice_report(xml_result,report_el) }, "xml" );
  }

  function show_guest_notice_report(xml_result,report_el) {
    var xml_doc = xml_result;
    var state = $(xml_doc).find('state').text();
    if ( state == 'Error' ) {
        $(report_el).removeClass(['hidden','bg-success']).addClass(['show','bg-warning']);
        $(report_el).append(document.createTextNode("There was an error."));
    }
    else if ( state =='NOOP' ) {
        $(report_el).removeClass(['hidden','bg-success']).addClass(['show','bg-warning']);
        $(report_el).append(document.createTextNode("Notice NOT sent."));
    }
    else {
        $(report_el).removeClass(['hidden','bg-warning']).addClass(['show','bg-success']);
        $(report_el).append(document.createTextNode("Notice sent."));
    }
  }
</script>
<?php include( $data['_config']['base_dir'] .'/view/doc-close.php' ); ?>
