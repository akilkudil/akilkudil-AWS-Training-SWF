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

$decisionTask = $swfClient->pollForDecisionTask(array(
    // domain is required
    'domain' => $domainName,
    // taskList is required
    'taskList' => array(
        // name is required
        'name' => $taskList,
    
    ),
	'reverseOrder'=> true,
));


$activityTypeVersion = "v1";
if (count($decisionTask["events"])>0){
    $taskToken = $decisionTask['taskToken'];
    $workFlowId = $decisionTask["workflowExecution"]["workflowId"];
    $runId = $decisionTask["workflowExecution"]["runId"];
    $lastEventId = $decisionTask["events"][0]["eventId"];
	$lastEventId = '"'.$lastEventId.'"';
	$eventType = $decisionTask["events"][0]['eventType'];	
	
    if($decisionTask["events"][0]["eventId"] == "3"){
		$eventType = 'WorkflowExecutionStarted';
	}
   	
	$continue_workflow = false;
    switch($eventType){
         case 'WorkflowExecutionStarted':                  
                  $nextActivity = "TalkToLead3";                  
                  $activityId = "1";
                  $continue_workflow = true;
                  break;
         case 'CompletedTalkToLead3Activity':
                  echo "Completed Workflow";
                  break;
				  }
    if($continue_workflow==true){
        echo "** scheduling activity task: ";
        $swfClient->respondDecisionTaskCompleted(
            array(
                  "taskToken"=>$taskToken,
                  "decisions"=>array(
                                    array(
                                         "decisionType"=>"ScheduleActivityTask",
                                         "scheduleActivityTaskDecisionAttributes"=>
                                                         array(
                                                              "activityType"=>array(
                                                                               "name" => $nextActivity,
                                                                               "version" => $activityTypeVersion
																			),
                                                              "activityId"=>$lastEventId,
                                                              "control"=>"This is a sample control message",
                                                              "scheduleToCloseTimeout" => "360",
                                                              "scheduleToStartTimeout" => "300",
                                                              "startToCloseTimeout" => "60",
                                                              "heartbeatTimeout" => "60", 
                                                              "taskList" => array(
                                                                                  "name" => $taskList
                                                                                  ),
                                                              "input" => "this is a sample input message"
                                                         )
                                    )
                               )
            )
        );
       
    }
    else{
        echo "signalling task complete ";
        $swfClient->respondDecisionTaskCompleted(array(
        "taskToken" => $taskToken,
        "decisions" => array(
            array(
                "decisionType" => "CompleteWorkflowExecution"
            )
        )
    ));
    }
    
}
else{
    echo "No Task Remaining";
}



?>