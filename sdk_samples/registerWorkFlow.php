<?php
require 'vendor/autoload.php';

use Aws\Swf\SwfClient;

$swfClient = SwfClient::factory(array(
    'profile' => 'default',
    'region'  => 'us-east-1',
    'version' => 'latest'
));

$domainName = "TestDomain";
$workFlowName = "testWorkflow";
$workFlowType = "nil";
$workFlowVersion = "1";
$taskList = "default";

function registerWorkflowTypes($domainName,$workFlowName, $workFlowVersion, $taskList, $swfClient){
     $workFlowTypes= $swfClient->listWorkflowTypes(array(
                                        // domain is required
                                       'domain' => $domainName,
                                       'name' => $workFlowName,
                                       // registrationStatus is required
                                       'registrationStatus' => 'REGISTERED'
                                       ));
     foreach($workFlowTypes['typeInfos'] as $workFlowType){
       if($workFlowType['workflowType']['version'] == $workFlowVersion){
           echo "workFlowType already registered";
           exit;
	}
     }
     $registeredWorkflow = $swfClient->registerWorkflowType(array(
                                      // domain is required
                                      'domain' => $domainName,
                                     // name is required
                                    'name' => $workFlowName,
                                    // version is required
                                    'version' => $workFlowVersion,
                                   'defaultTaskList' => array(
                                   // name is required
                                   'name' => $taskList,
                                   )
                                 ));
    return $registeredWorkflow;

}

$registeredWorkflow = registerWorkflowTypes($domainName,$workFlowName, $workFlowVersion, $taskList, $swfClient);
if($registeredWorkflow){
   echo "Workflow successfully registered";
}





?>