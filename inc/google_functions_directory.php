<?php
function errorTraps($n,$e,$ecode,$emessage){
	//error_log( 'An error occurred: ' . $emessage );
	if($ecode == 403 && (strpos($emessage, 'Rate Limit Exceeded')>0 || strpos($emessage,'RateLimitExceeded')>0)) {
		//Apply exponential backoff.
		error_log( 'Rate Limit Exceeded Backoff ' . $n );
		usleep((1 << $n) * 1000 + rand(0, 1000));
	}elseif($ecode == 404 && (strpos($emessage, 'Permission not found'))){
		//Apply exponential backoff.
		error_log( 'Permission Error Backoff ' . $n );
		usleep((1 << $n) * 1000 + rand(0, 1000));
	}elseif($ecode == 503 && (strpos($emessage, 'Backend Error'))){
		//Apply exponential backoff.
		error_log( 'Backend Error Backoff ' . $n );
		usleep((1 << $n) * 1000 + rand(0, 1000));
	}elseif($ecode == 503 && (strpos($emessage, 'Service unavailable'))){
		//Apply exponential backoff.
		error_log( 'Service unavailable Backoff ' . $n );
		usleep((1 << $n) * 1000 + rand(0, 1000));
	}elseif($ecode == 500 && (strpos($emessage, 'Internal Error'))){
		//Apply exponential backoff.
		error_log( 'Internal Error Backoff ' . $n );
		usleep((1 << $n) * 1000 + rand(0, 1000));
	}elseif($ecode == 403 && (strpos($emessage, 'Insufficient permissions'))){
		//Apply exponential backoff.
		error_log( 'Insufficient permissions Error Backoff ' . $n );
		//usleep((1 << $n) * 1000 + rand(0, 1000));
	}elseif($ecode == 401 && (strpos($emessage, 'Unauthorized'))){
		//Apply exponential backoff.
		error_log( 'Unauthorized Error Backoff ' . $n );
		//usleep((1 << $n) * 1000 + rand(0, 1000));
	}elseif($ecode == 400){
		error_log( $emessage );
		exit(222);
	}else{
		// Other error, re-throw.
		throw $e;
	}
	return $e;
}

function buildService($userEmail,$userId,$PKCS12FilePath,$scope) {
	$key = file_get_contents($PKCS12FilePath);
	$auth = new Google_Auth_AssertionCredentials(
		$userId,
		$scope,
		$key);
	$auth->sub = $userEmail;
	$client = new Google_Client();
	$client->setAssertionCredentials($auth);
	if(isset($_SESSION['GoogleToken'])) {
		$client->setAccessToken($_SESSION['GoogleToken']);
	}
	return $client;
}

function getOrgList($client, $userEmail, $pageToken=null, $query=null){
	$max=500;
	if($query){
		$query = '&query='. $query;
	}
	if($pageToken){
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&maxResults=$max&fields=nextPageToken%2Cusers(orgUnitPath%2CprimaryEmail)&pageToken=$pageToken$query");
	}else{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&maxResults=$max&fields=nextPageToken%2Cusers(orgUnitPath%2CprimaryEmail)$query");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getAuth()->authenticatedRequest($req);
	return $val->getResponseBody();
}

function getListOrgs($client, $userEmail, $pageToken=null){
	$max=500;
	if($pageToken){
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/customer/my_customer/orgunits?type=all&pageToken=$pageToken");
	}else{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/customer/my_customer/orgunits?type=all");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getAuth()->authenticatedRequest($req);
	return $val->getResponseBody();
}

function getGroupList($client, $userEmail, $pageToken=null){
	$max=200;
	if($pageToken){
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/groups/?customer=my_customer&pageToken=$pageToken&maxResults=$max");
	}else{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/groups/?customer=my_customer&maxResults=$max");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getAuth()->authenticatedRequest($req);
	return $val->getResponseBody();
}

function getGroupMembers($client, $groupKey, $pageToken=null){
	$max = 200;
	if($pageToken){
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/groups/$groupKey/members?pageToken=$pageToken");
	}else{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/groups/$groupKey/members");
	}
	$req->setRequestMethod('get');
	$headers	= array('GData-Version'=>'3.0',
				'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getAuth()->authenticatedRequest($req);
	return $val->getResponseBody();
	//return $val;
}

function addUser($client,$user){
	try{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users");
		$req->setRequestMethod('post');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($user);
		$val		= $client->getAuth()->authenticatedRequest($req);
		//return $val;
		return json_decode($val->getResponseBody(), true);
	}catch (Exception $e){
		print_r($e);
	}
}

function addOrgUnit($client,$postBody){
	try{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/customer/my_customer/orgunits");
		$req->setRequestMethod('post');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($postBody);
		$val = $client->getAuth()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function getUser($client,$email){
	//echo 'Trying getUser' . "\r\n";
	$requestBody = $email;
	$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/$email?projection=full");
	$req->setRequestMethod('get');
	$headers = array('GData-Version'=>'3.0',
	'Content-type'=>'application/atom+xml');
	$req->setRequestHeaders($headers);
	$val		= $client->getAuth()->authenticatedRequest($req);
	if(@$val->error){
		throw new Exception($val->error);
	}
	return $val->getResponseBody();
}

function updateUser($client,$email,$requestBody){
	for($n = 0; $n < 10; ++$n){
		try{
			$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/$email");
			$req->setRequestMethod('put');
			$headers = array('GData-Version'=>'3.0',
			'Content-type'=>'application/json');
			$req->setRequestHeaders($headers);
			$req->setPostBody($requestBody);
			$val = $client->getAuth()->authenticatedRequest($req);
			return $val->getResponseBody();
		}catch (Exception $e){
			print_r($e);
		}
	}
}

function deleteUser($client,$email){
	try{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/$email");
		$req->setRequestMethod('delete');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$val = $client->getAuth()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function getAllUsers($client,$email,$pageToken=null){
	for($n = 0; $n < 10; ++$n){
		try{
			$max=500;
			if($pageToken){
				$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&pageToken=$pageToken&maxResults=$max&orderBy=email&sortOrder=descending&projection=full");
			}else{
				$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&maxResults=$max&orderBy=email&sortOrder=descending&projection=full");
			}
			$req->setRequestMethod('get');
			$headers	= array('GData-Version'=>'3.0',
						'Content-type'=>'application/atom+xml');
			$req->setRequestHeaders($headers);
			$val		= $client->getAuth()->authenticatedRequest($req);
			if(@$val->error){
				throw new Exception($val->error);
			}
			return $val->getResponseBody();
		}catch (Exception $e){
			errorTraps($n, $e, $e->getCode(), $e->getMessage());
		}
	}
}

function searchUsers($client,$email,$query=null,$pageToken=null){
    if($query){
        $Query='&query='.$query;
    }
	for($n = 0; $n < 10; ++$n){
		try{
			$max=500;
			if($pageToken){
				$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&pageToken=$pageToken&maxResults=$max&orderBy=email&sortOrder=descending$Query");
			}else{
				$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/users/?customer=my_customer&maxResults=$max&orderBy=email&sortOrder=descending$Query");
			}
			$req->setRequestMethod('get');
			$headers	= array('GData-Version'=>'3.0',
						'Content-type'=>'application/atom+xml');
			$req->setRequestHeaders($headers);
			$val		= $client->getAuth()->authenticatedRequest($req);
			return $val->getResponseBody();
		}catch (Exception $e){
			errorTraps($n, $e, $e->getCode(), $e->getMessage());
		}
	}
}

function createGroup($client,$postBody){
	try{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/groups/?customer=my_customer");
		$req->setRequestMethod('post');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($postBody);
		$val = $client->getAuth()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function groupSettings($client,$groupEmail,$postBody){
	try{
		$req = new Google_Http_Request("https://www.googleapis.com/groups/v1/groups/$groupEmail");
		$req->setRequestMethod('put');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($postBody);
		$val = $client->getAuth()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function addGroupMembers($client,$groupEmail, $postBody){
	try{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/groups/$groupEmail/members");
		$req->setRequestMethod('post');
		$headers = array('GData-Version'=>'3.0',
		'Content-type'=>'application/json');
		$req->setRequestHeaders($headers);
		$req->setPostBody($postBody);
		$val = $client->getAuth()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function removeGroupMembers($client,$groupEmail, $member){
	try{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/groups/$groupEmail/members/$member");
		$req->setRequestMethod('DELETE');
		//$headers = array('GData-Version'=>'3.0','Content-type'=>'application/json');
		//$req->setRequestHeaders($headers);
		//$req->setPostBody($postBody);
		$val = $client->getAuth()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function getUsersGroups($client, $member){
	try{
		$req = new Google_Http_Request("https://www.googleapis.com/admin/directory/v1/groups?userKey=$member&maxResults=200");
		$req->setRequestMethod('get');
		$headers	= array('GData-Version'=>'3.0',
					'Content-type'=>'application/atom+xml');
		$req->setRequestHeaders($headers);
		$val		= $client->getAuth()->authenticatedRequest($req);
		return $val->getResponseBody();
	}catch (Exception $e){
		print_r($e);
	}
}

function checkStaff($data){
    $isStaff = true;
    $checkOrg = array('Students', 'NonUsers');
    $checkEmail = array('apple','ipad','_','-','dosurvey','android','chromebook','coral.cliffs','dhms','dixiesunteacheraward','ecse.sec','ees.preschool','ged-pcf','hes.admin','hhsadmin','help.help','ifas@washk12.org','wifiguest','trips@washk12.org','trans.sped','testuser','temp.secretary','teacher.new','stars@washk12.org','southwest','snow.canyon','pvhs','panorama.elementary.school');
    foreach($checkOrg as $org){
        if(preg_match('/'.$org."/", $data['orgUnitPath'])){
            $isStaff=false;
        }
    }
    foreach($checkEmail as $org){
        if(preg_match('/'.$org."/", $data['primaryEmail'])){
            $isStaff=false;
        }
    }
    if(is_numeric(substr($data['primaryEmail'],0,2))){
        $isStaff=false;
    }
    if($data['suspended']==1){
        $isStaff=false;
    }
    return $isStaff;
}

function checkStudents($data){
    $isStaff = false;
    $checkOrg = array('NonUsers');
    $checkEmail = array('apple','ipad','_','android','chromebook','coral.cliffs','dhms','dixiesunteacheraward','ecse.sec','ees.preschool','ged-pcf','hes.admin','hhsadmin','help.help','ifas@washk12.org','wifiguest','trips@washk12.org','trans.sped','testuser','temp.secretary','teacher.new','stars@washk12.org','southwest','snow.canyon','pvhs','panorama.elementary.school');
    foreach($checkOrg as $org){
        if(preg_match('/'.$org."/", $data['orgUnitPath'])){
            $isStaff=false;
        }
    }
    foreach($checkEmail as $org){
        if(preg_match('/'.$org."/", $data['primaryEmail'])){
            $isStaff=false;
        }
    }
    if(is_numeric(substr($data['primaryEmail'],0,2)) && preg_match('/Students/', $data['orgUnitPath'])){
        $isStaff=true;
    }
    if($data['suspended']==1){
        $isStaff=false;
    }
    return $isStaff;
}
?>
