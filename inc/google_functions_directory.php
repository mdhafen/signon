<?php
function buildService($userEmail,$SCOPE) {
	global $SERVICE_ACCOUNT_EMAIL, $SERVICE_ACCOUNT_PKCS12_FILE_PATH;
	$key = file_get_contents($SERVICE_ACCOUNT_PKCS12_FILE_PATH);
	$auth = new Google_AssertionCredentials(
		$SERVICE_ACCOUNT_EMAIL,
		array($SCOPE),
		$key);
	$auth->sub = $userEmail;
	$client = new Google_Client();
	$client->setUseObjects(true);
	$client->setAssertionCredentials($auth);
	if(isset($_SESSION['token'])) {
		$client->setAccessToken($_SESSION['token']);
	}
	return $client;
}

function getOrgList($client, $userEmail, $pageToken=null, $query=null){
	$max=500;
	if($query){
		$query = '&query='. $query;
	}
	if($pageToken){
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&maxResults=$max&fields=nextPageToken%2Cusers(orgUnitPath%2CprimaryEmail)&pageToken=$pageToken$query");
	}else{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&maxResults=$max&fields=nextPageToken%2Cusers(orgUnitPath%2CprimaryEmail)$query");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getIo()->authenticatedRequest($req);
	return $val->getResponseBody();
}

function getListOrgs($client, $userEmail, $pageToken=null){
	$max=500;
	if($pageToken){
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/customer/my_customer/orgunits?type=all&pageToken=$pageToken");
	}else{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/customer/my_customer/orgunits?type=all");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getIo()->authenticatedRequest($req);
	return $val->getResponseBody();
}

function getGroupList($client, $userEmail, $pageToken=null){
	$max=200;
	if($pageToken){
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/groups/?customer=my_customer&pageToken=$pageToken&maxResults=$max");
	}else{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/groups/?customer=my_customer&maxResults=$max");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getIo()->authenticatedRequest($req);
	return $val->getResponseBody();
}

function getGroupMembers($client, $groupKey, $pageToken=null){
	$max = 200;
	if($pageToken){
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/groups/$groupKey/members?pageToken=$pageToken");
	}else{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/groups/$groupKey/members");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getIo()->authenticatedRequest($req);
	return $val->getResponseBody();
	//return $val;
}

function addUser($client,$user){
	try{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/users");
		$req->setRequestMethod('post');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($user);
		$val		= $client->getIo()->authenticatedRequest($req);
		//return $val;
		return json_decode($val->getResponseBody(), true);
	}catch (Exception $e){
		print_r($e);
	}
}

function addOrgUnit($client,$postBody){
	try{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/customer/my_customer/orgunits");
		$req->setRequestMethod('post');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($postBody);
		$val = $client->getIo()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function getUser($client,$email){
		$requestBody = $email;
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/users/$email");
		$req->setRequestMethod('get');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/atom+xml');
		$req->setRequestHeaders($headers);
		$val		= $client->getIo()->authenticatedRequest($req);
		return json_decode($val->getResponseBody(), true);
}

function updateUser($client,$email,$requestBody){
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/users/$email");
		$req->setRequestMethod('put');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($requestBody);
		$val		= $client->getIo()->authenticatedRequest($req);
		return $val->getResponseBody();
}

function getAllUsers($client,$email,$pageToken=null){
	$max=500;
	if($pageToken){
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&pageToken=$pageToken&maxResults=$max");
	}else{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&maxResults=$max");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getIo()->authenticatedRequest($req);
	return $val->getResponseBody();
}

function createGroup($client,$postBody){
	try{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/groups/?customer=my_customer");
		$req->setRequestMethod('post');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($postBody);
		$val = $client->getIo()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function groupSettings($client,$groupEmail,$postBody){
	try{
		$req = new Google_HttpRequest("https://www.googleapis.com/groups/v1/groups/$groupEmail");
		$req->setRequestMethod('put');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($postBody);
		$val = $client->getIo()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function addGroupMembers($client,$groupEmail, $postBody){
	try{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/groups/$groupEmail/members");
		$req->setRequestMethod('post');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($postBody);
		$val = $client->getIo()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function removeGroupMembers($client,$groupEmail, $member){
	try{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/groups/$groupEmail/members/$member");
		$req->setRequestMethod('DELETE');
		//$headers = array('GData-Version'=>'3.0','Content-type'=>'application/json');
		//$req->setRequestHeaders($headers);
		//$req->setPostBody($postBody);
		$val = $client->getIo()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function getUsersGroups($client, $member){
	try{
		$req = new Google_HttpRequest("https://www.googleapis.com/admin/directory/v1/groups?userKey=$member&maxResults=200");
		$req->setRequestMethod('get');
		$headers	= array('GData-Version'=>'3.0',
					'Content-type'=>'application/atom+xml');
		$req->setRequestHeaders($headers);
		$val		= $client->getIo()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}
?>
