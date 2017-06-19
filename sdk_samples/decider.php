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
    )
));


$activityTypeVersion = "1";
if (count($decisionTask["events"])>0){
    $taskToken = $decisionTask['taskToken'];
    $workFlowId = $decisionTask["workflowExecution"]["workflowId"];
    $runId = $decisionTask["workflowExecution"]["runId"];
    $lastEventId = $decisionTask["events"][0]['eventId'];
	$lastEventId = '"'.$lastEventId.'"';
    $continue_workflow = false;
    switch($decisionTask["events"][0]['eventType']){
         case 'WorkflowExecutionStarted':                  
                  $nextActivity = "TestAct1";                  
                  $activityId = "1";
                  $continue_workflow = true;
                  break;
         case 'WorkflowExecutionStartednn':
                  echo "WorkflowExecutionStarted is event";
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