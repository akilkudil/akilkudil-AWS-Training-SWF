<?php
require 'vendor/autoload.php';

use Aws\Swf\SwfClient;

$swfClient = SwfClient::factory(array(
    'profile' => 'default',
    'region'  => 'us-east-1',
    'version' => 'latest'
));

$domainName = "MyNeatNewDomain";
$description = "myDomain";
$retentionPeriod = "0";

$listDomains = $swfClient->listDomains(array(
     // registrationStatus is required
    'registrationStatus' => 'REGISTERED'
));

foreach ($listDomains['domainInfos'] as $domain){
   if($domain['name'] == $domainName){
       echo "Domain name already registered";
       exit;   
   }
   
}

$result = $client->registerDomain(array(
    // name is required
    'name' => $domainName,
    'description' => $description,
    'workflowExecutionRetentionPeriodInDays' => $retentionPeriod
));

?>