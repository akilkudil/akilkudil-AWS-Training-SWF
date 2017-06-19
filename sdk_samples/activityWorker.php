<?php

require 'vendor/autoload.php';

use Aws\Swf\SwfClient;

$swfClient = SwfClient::factory(array(
    'profile' => 'default',
    'region'  => 'us-east-1',
    'version' => 'latest'
));

$domainName = "TestDomain";
$taskList = "default";

$activityTask = $swfClient->pollForActivityTask(array(
    // domain is required
    'domain' => $domainName,
    // taskList is required
    'taskList' => array(
        // name is required
        'name' => $taskList,
    )
));

if($taskToken = $activityTask["taskToken"]){
	$activityName = $activityTask["activityType"]["name"];
	$taskCompleted = false;
	$workflowId = $activityTask["workflowExecution"]["workflowId"];
	$runId =  $activityTask["workflowExecution"]["runId"];
	switch($activityName){
		case "TestAct1":
		    $taskCompleted = true;
		break;
	}
	if($taskCompleted){
		$swfClient->respondActivityTaskCompleted(array(
                         "taskToken" => $taskToken,
                         "result" => "$activityName activity completed (workFlowId:$workflowId, runId:$runId)"
                         ));
	}
	
}
else{
	echo "No activity found";
}

?>