<?php
require 'vendor/autoload.php';

use Aws\Swf\SwfClient;

$swfClient = SwfClient::factory(array(
    'profile' => 'default',
    'region'  => 'us-east-1',
    'version' => 'latest'
));

$domainName = "TestDomain";
$activityName = "TestAct1";
$activityType = "nil";
$activityVersion = "1";
$taskList = "default";

$activityTypes = $swfClient->listActivityTypes(array(
    // domain is required
    'domain' => $domainName,
    'name' => $activityName,
    // registrationStatus is required
    'registrationStatus' => 'REGISTERED'
));

foreach($activityTypes['typeInfos'] as $activityType){
      
   if($activityType['activityType']['version'] == $activityVersion){
           echo "activityType already registered";
           exit;
	}
       
     }
$registeredActivity = $swfClient->registerActivityType(array(
    // domain is required
    'domain' => $domainName,
    // name is required
    'name' => $activityName,
    // version is required
    'version' => $activityVersion,
    'defaultTaskList' => array(
        // name is required
        'name' => $taskList,
    )
));

if($registeredActivity){
	echo "Activity already registered";
	}

?>