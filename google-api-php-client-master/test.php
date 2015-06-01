<html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
<?php
require 'src/Google/autoload.php';

session_start();

$service_account_name = "987694720165-oeqomp6saoe1q258ohn4kfg1h2erp2ih@developer.gserviceaccount.com";
$key_file_location = "YouthDecides-Member-Search-140097cbf211.p12";

$client = new Google_Client();

$client->setApplicationName("Members");

$directory = new Google_Service_Directory($client);

if (isset($_SESSION['service_token']) && $_SESSION['service_token']) {
  $client->setAccessToken($_SESSION['service_token']);
}

$key = file_get_contents($key_file_location);

$cred = new Google_Auth_AssertionCredentials(
  // Replace this with the email address from the client.
  $service_account_name,
  // Replace this with the scopes you are requesting.
  array('https://www.googleapis.com/auth/admin.directory.user'),
  $key
);
$cred->sub = "alaa@youthdecides.org";
$client->setAssertionCredentials($cred);

if ($client->getAuth()->isAccessTokenExpired()) {
  $client->getAuth()->refreshTokenWithAssertion($cred);
}
$_SESSION['service_token'] = $client->getAccessToken();
$param = array();
$param['domain'] = "youthdecides.org";

$email = "alaa@youthdecides.org";
$r = $directory->users->listUsers($param);
foreach($r as $key => $value){
  echo $value['name']['fullName'].'<br>';
  echo $value['primaryEmail'].'<br><br>';
  if(isset($value['thumbnailPhotoUrl'])){
    $photo = $directory->users_photos->get($value['primaryEmail']);
    if(isset($photo['photoData'])){
      $imgData = strtr($photo['photoData'], '-_*', '+/=');
      echo "<img src='data:image/jpeg;base64, $imgData' /><br><br>";
    }
  }
}
/*if($r) {
     echo "Name: ".$r->name->fullName."<br/>";
     echo "Suspended?: ".(($r->suspended === true) ? 'Yes' : 'No')."<br/>";
     echo "Org/Unit/Path: ".$r->orgUnitPath."<br/>";
} else {
     echo "User does not exist: $email<br/>";
     // if the user doesn't exist, it's safe to create the new user
}*/

