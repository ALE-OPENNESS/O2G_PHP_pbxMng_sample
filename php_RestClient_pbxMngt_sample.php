<html>
<head>
<title>A basic pbx management subscriber creation example and subscriber modification (Facility_Category)</title>
<?php

function createSubscriber() {
	$server = $_POST['Server'];
	$serverUrl = "http://" . $server . "/api/rest/";
	$adminlogin = $_POST['Adminlogin'];
	$password =$_POST['Password'];
	$nodeId =$_POST['NodeId'];
	$subscriberNumber = $_POST['Subscriber'];
	$subscriberName = 'Lagaffe';
	$subscriberFirstName = 'Gaston';
	$verbose = fopen('php://temp', 'w+');

	############################################
	####  Authentication with user/password ####
	############################################

	$ch_auth = curl_init();
		echo "Begin authent on $serverUrl !\n";

	// receive server response ...
	curl_setopt($ch_auth, CURLOPT_RETURNTRANSFER, true);
	#curl_setopt($ch_auth, CURLOPT_VERBOSE, true);
	#curl_setopt($ch_auth, CURLOPT_STDERR, $verbose);

	curl_setopt($ch_auth, CURLOPT_USERPWD, $adminlogin . ":" . $password);
	curl_setopt($ch_auth, CURLOPT_URL, $serverUrl . "authenticate?version=1.0");
	// save cookie in tmp file
	curl_setopt($ch_auth, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');

	$result = curl_exec ($ch_auth);
	if ($result === FALSE) {
		printf("cUrl error on authent(#%d): %s<br>\n", curl_errno($handle),
			   htmlspecialchars(curl_error($handle)));
	}
	echo "End authent !\n";



	#############################
	####  Create session	 ####
	#############################

	echo "Begin create session !\n";
	$ch_session = curl_init();
	curl_setopt($ch_session, CURLOPT_VERBOSE, true);
	curl_setopt($ch_session, CURLOPT_STDERR, $verbose);

	curl_setopt($ch_session, CURLOPT_URL, $serverUrl . "1.0/sessions");
	curl_setopt($ch_session, CURLOPT_POST, 1);
	// set cookie
	curl_setopt($ch_session, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
	// set mandatory applicationName
	$appName = array("applicationName" => "testPHP");                                                                    
	$appName_string = json_encode($appName);
	curl_setopt($ch_session, CURLOPT_POSTFIELDS, $appName_string);                                                                  
	curl_setopt($ch_session, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($appName_string))                                                                       
	);                                                                                   

	$result = curl_exec ($ch_session);
	if ($result === FALSE) {
		printf("cUrl error on create session(#%d): %s<br>\n", curl_errno($handle),
			   htmlspecialchars(curl_error($handle)));
	}
	echo "End create session !\n";


	#############################
	####  create susbcriber	 ####
	#############################

	echo "Begin create subscriber !\n";
	$ch_createSubs = curl_init();
	curl_setopt($ch_createSubs, CURLOPT_VERBOSE, true);
	curl_setopt($ch_createSubs, CURLOPT_STDERR, $verbose);

	curl_setopt($ch_createSubs, CURLOPT_URL, $serverUrl . "1.0/pbxs/" . $nodeId . "/instances/Subscriber");
	curl_setopt($ch_createSubs, CURLOPT_POST, 1);
	// set cookie
	curl_setopt($ch_createSubs, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
	// set parameters
	$createSubsReqParams = '{"attributes": [{"name": "Directory_Number","value": ["' . $subscriberNumber . '"]},{"name": "Annu_First_Name","value": ["' . $subscriberFirstName . '"]},{"name": "Annu_Name","value": ["' . $subscriberName . '"]},{"name": "Station_Type","value": ["NOE_C_IP"]}]}';                                                               
	curl_setopt($ch_createSubs, CURLOPT_POSTFIELDS, $createSubsReqParams);                                                                  
	curl_setopt($ch_createSubs, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($createSubsReqParams))                                                                       
	);                                                                                   

	$result = curl_exec ($ch_createSubs);
	if ($result === FALSE) {
		printf("cUrl error on create subscriber(#%d): %s<br>\n", curl_errno($handle),
			   htmlspecialchars(curl_error($handle)));
	}
	echo "End create subscriber  call !\n";
	
	
    #############################
	####  modify susbcriber	 ####
	#############################

	echo "Begin modify subscriber !\n";
	$ch_modifySubs = curl_init();
	curl_setopt($ch_modifySubs, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch_modifySubs, CURLOPT_VERBOSE, true);
	curl_setopt($ch_modifySubs, CURLOPT_STDERR, $verbose);

	curl_setopt($ch_modifySubs, CURLOPT_URL, $serverUrl . "1.0/pbxs/" . $nodeId . "/instances/Subscriber/" . $subscriberNumber);
//	curl_setopt($ch_modifySubs, CURLOPT_PUT, 1);	//doesn't work !! (body is not sent)
	curl_setopt($ch_modifySubs, CURLOPT_CUSTOMREQUEST, "PUT");
	// set cookie
	curl_setopt($ch_modifySubs, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
	// set  parameters: set Facility_Category to "1"
	$modifySubsReqParams = '{"attributes": [{"name": "Facility_Category_Id","value": ["1"]}]}';                                                               
	curl_setopt($ch_modifySubs, CURLOPT_POSTFIELDS, $modifySubsReqParams);                                                                  
 	curl_setopt($ch_modifySubs, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json')                                                                       
	);
 	
	$result = curl_exec ($ch_modifySubs);
	if ($result === FALSE) {
		printf("cUrl error on modify subscriber(#%d): %s<br>\n", curl_errno($handle),
			   htmlspecialchars(curl_error($handle)));
	}
	echo "End modify subscriber  call !\n";

	#############################
	####  Close session	 ####
	#############################

	echo "Begin close session !\n";

	curl_setopt($ch_session, CURLOPT_CUSTOMREQUEST, "DELETE");
	// set cookie
	curl_setopt($ch_session, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');

	$result = curl_exec ($ch_session);
	if ($result === FALSE) {
		printf("cUrl error on delete session(#%d): %s<br>\n", curl_errno($handle),
			   htmlspecialchars(curl_error($handle)));
	}
	echo "End close session !\n";


rewind($verbose);
$verboseLog = stream_get_contents($verbose);

echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";


curl_close ($ch_auth);
curl_close ($ch_session);
curl_close ($ch_createSubs);
curl_close ($ch_modifySubs);


}
?>
</head>
<body>
<h2> This is an example of subscriber creation on a PHP server</h2>
<ul>
<li> preliminary, enter the server address, the admin user login, its password, the subscriberNumber device to create, the pbx node number </li>
<li> then click to authenticate the user, to start the session (thanks to the returned cookie)and send the susbscriber creation request</li>
</ul>

<FORM METHOD ="POST" ACTION = "php_RestClient_pbxMngt_sample.php">

    <P>
    <LABEL for="serverIP">Server address </LABEL>
              <INPUT type="text" name ="Server" value = "vm-roxel2.bstlabrd.fr.alcatel-lucent.com"> <BR>
    <LABEL for="adminlogin">Admin login name </LABEL>
              <INPUT type="text" name ="Adminlogin" value = "roxeAdmin">  <BR>
    <LABEL for="password">Admin password </LABEL>
              <INPUT type="password" name="Password" value = ""><BR>
    <LABEL for="susbcriber">Pbx node identifier </LABEL>
              <INPUT type="text" name="NodeId" value = "7"><BR>
    <LABEL for="susbcriber">Subscriber number to create </LABEL>
              <INPUT type="text" name="Subscriber" value = "70300"><BR>
 			  
	<INPUT TYPE = "Submit" Name = "Create" VALUE = "createSubscriber">

    
    </P>
 </FORM>
         <?php
           if($_SERVER['REQUEST_METHOD']=='POST')
           {
               createSubscriber();
           } 
        ?>
  
</body>
</html>
