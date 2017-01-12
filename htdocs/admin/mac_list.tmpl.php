<?php include($data['_config']['base_dir'] .'/htdocs/doc-open.php'); ?>
<title><?= $data['_config']['site_title'] ?> - Register Lab Device</title>
<link rel="stylesheet" type="text/css" href="<?= $data['_config']['base_url'] ?>css/datatables.min.css"/>
<script src="<?= $data['_config']['base_url'] ?>js/list.min.js"></script>
<script src="<?= $data['_config']['base_url'] ?>js/list.pagination.min.js"></script>
<style>
.paginationTop li, .paginationBottom li {
  display: inline-block;
  list-style: none;
  padding-right: 10px;
}

.paginationTop li.active a, .paginationBottom li.active a {
  color: #000;
}

.paginationTop li.disabled a, .paginationBottom li.disabled a,
.paginationTop li.disabled a:hover, .paginationBottom li.disabled a:hover {
  color: #000;
  cursor: default;
  text-decoration: none;
}

.list_sort:after {
  width: 0;
  height: 0;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-bottom: 5px solid transparent;
  content:"";
  position: relative;
  top:-10px;
  right:-5px;
}

.list_sort.asc:after {
  width: 0;
  height: 0;
  content:"";
  position: relative;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-bottom: 5px solid #000;
  top:-15px;
  right:-5px;
}

.list_sort.desc:after {
  width: 0;
  height: 0;
  content:"";
  position: relative;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid #000;
  top:13px;
  right:-5px;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$('body .dropdown-toggle').dropdown();
});
</script>
<?php
include( $data['_config']['base_dir'] .'/htdocs/doc-head-close.php' );
include( $data['_config']['base_dir'] .'/htdocs/doc-header.php' );
?>

<div class="container">
<div class="main-page">

<h1>List Of Registered Lab Device</h1>

<?php if ( ! empty($data['deleted']) ) { ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<p>M.A.C. removed from register!</p>
</div>
<?php } ?>

<div class="panel panel-default panel-body">
<div class="table-responsive" id="mac_table_container">

<ul class="paginationTop"></ul>
<table class="table table-striped" id="mac_list">
<thead>
<tr>
<th>
    <span class="list_sort" data-sort="list_mac">M.A.C. Address</span><br>
    <input type="text" id="mac_mac_filter" size="12" onkeyup="list_obj.search(this.value,['list_mac'])">
</th>
<th><span class="list_sort" data-sort="list_user">Registered By</span></th>
<th><span class="list_sort" data-sort="list_home">Device Home</span></th>
<th><span class="list_sort" data-sort="list_desc">Device Description</span></th>
<th><span class="list_sort" data-sort="list_date">Date Registered</span></th>
<th></th>
</tr>
</thead>
<tbody class="list">
<?php foreach ( $data['mac_list'] as $mac_row ) { ?>

<tr>
<td class="list_mac"><?= $mac_row['macaddress'] ?></td>
<td class="list_user"><?= $mac_row['submitted_user'] ?></td>
<td class="list_home"><?= $mac_row['device_home'] ?></td>
<td class="list_desc"><?= $mac_row['submitted_desc'] ?></td>
<td class="list_date"><?= $mac_row['submitted_date'] ?></td>
<td class="list_action">
  <form method="post" action="register.php" class="form-horizontal" method="post" enctype="multipart/form-data">
  <input type="hidden" name="client_mac" value="<?= $mac_row['macaddress'] ?>">
  <input type="submit" name="op" value="Delete" class="form-control">
  </form>
</td>
</tr>
<?php } ?>
</tbody>
</table>
<ul class="paginationBottom"></ul>

</div>
</div>

</div>
</div>

<script>
var list_options = {
  valueNames: [ 'list_mac','list_user','list_home','list_desc','list_date','list_action' ],
  searchClass: 'list_search',
  sortClass: 'list_sort',
  //indexAsync: true,
  page: 10,
  plugins: [
    ListPagination({
      name: "paginationTop",
      paginationClass: "paginationTop",
      innerWindow: 2,
      outerWindow: 1
    }),
    ListPagination({
      name: "paginationBottom",
      paginationClass: "paginationBottom",
      innerWindow: 2,
      outerWindow: 1
    })
  ]
};

var list_obj = new List('mac_table_container', list_options);
</script>

<?php include( $data['_config']['base_dir'] .'/htdocs/doc-close.php' ); ?>
