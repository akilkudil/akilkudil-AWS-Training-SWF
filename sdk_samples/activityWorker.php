<?php

require 'vendor/autoload.php';
require 'PHPMailer/PHPMailerAutoload.php';

use Aws\Swf\SwfClient;

$swfClient = SwfClient::factory(array(
    'profile' => 'default',
    'region'  => 'us-east-1',
    'version' => 'latest'
));

$domainName = "TestDomain";
$taskList = "default";
//while(true){
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
		case "TalkToLead3":
		    $mail = new PHPMailer;
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->SMTPAuth = true; // enable SMTP authentication
			$mail->SMTPSecure = "ssl"; // sets the prefix to the servier
			$mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server
			$mail->Port = 465; // set the SMTP port for the GMAIL server
			$mail->Username = "akilan.ideas2it@gmail.com"; // GMAIL username
			$mail->Password = "xxxxxxxx"; // GMAIL password
			

			//Typical mail data
			$mail->AddAddress("akilkudil@gmail.com", "Akilan");
			$mail->SetFrom("akilan.ideas2it@gmail.com", "Ideas2it");
			$mail->Subject = "Workflow status";
			$mail->Body = "TalkToLead3 completed";
			try{
				$mail->Send();
				echo "Activity- Mail Success!<\n>";
			} catch(Exception $e){
				//Something went bad
				echo "Fail - " . $mail->ErrorInfo."<\n>";
			}
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
	echo "ActivityWorker - No activity found<\n>";
}
//}
?>