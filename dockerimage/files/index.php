<?php

$osTicketURL = getenv("OSTICKET_URL");
$osTicketAPIToken = getenv("OSTICKET_API_KEY");

if ($osTicketURL == null || $osTicketAPIToken == null) exit("Missing mandatory input parameters OSTICKET_URL and / or OSTICKET_API_KEY");

$entityBody = file_get_contents('php://input');

if ($entityBody == null) exit("Missing data input from Keptn. Exiting.");

// Write the raw input to the log file...
$logFile = fopen("logs/osticketIncomingEvents.log", "a") or die("Unable to open file!");
fwrite($logFile, $entityBody . "\n");
fclose($logFile);

//Decode the incoming JSON event
$cloudEvent = json_decode($entityBody);

// Format result span coloring
$result = strtoupper($cloudEvent->{'data'}->{'result'});
$resultSpan = "<span>" . $result . "<span>";
if ($result == 'PASS') $resultSpan = "<span style='background: green; color: white;'>" . $result . "</span>";
if ($result == 'WARNING') $resultSpan = "<span style='background: orange; color: white;'>" . $result . "</span>";
if ($result == 'FAIL') $resultSpan = "<span style='background: red; color: white;'>" . $result . "</span>";

// Build JSON for osTicket
$osTicketObj = new stdClass();
$osTicketObj->source = "API";
$osTicketObj->email = "keptn@keptn.sh";
$osTicketObj->name = "Keptn Quality Gate";$cloudEvent->{'data'}->{'project'} . " - " . $cloudEvent->{'data'}->{'service'} . " - " . $cloudEvent->{'data'}->{'stage'};
$osTicketObj->subject = "Test Result: " . $result;
$osTicketObj->message = "data:text/html,";
$osTicketObj->message .= "<h3>Keptn Test Run Completed<br />Result: " . $resultSpan . "</h3>";
$osTicketObj->message .= "Project: <strong>" . $cloudEvent->{'data'}->{'project'} . "</strong><br />Service: <strong>" . $cloudEvent->{'data'}->{'service'} . "</strong><br />Stage: <strong>" . $cloudEvent->{'data'}->{'stage'} . "</strong><br />";

// For loop through indicatorResults
$osTicketObj->message .= "<br /><br /><strong><u>SLI Results</u></strong><br /><br />";
foreach ($cloudEvent->{'data'}->{'evaluationdetails'}->{'indicatorResults'} as &$value) {
  $osTicketObj->message .= "Metric: <strong>" . $value->{'value'}->{'metric'} . "</strong><br />";
  $osTicketObj->message .= "Status: <strong>" . $value->{'status'} . "</strong><br />";
  $osTicketObj->message .= "Value: <strong>" . $value->{'value'}->{'value'} . "</strong><br />";

  $osTicketObj->message .= "<br /><br /><strong>Targets</strong><br />";
  foreach ($value->{'targets'} as &$target) {
      $osTicketObj->message .= "Criteria: " . $target->{'criteria'} . "<br />";
      $osTicketObj->message .= "Target Value: <strong>" . $target->{'targetValue'} . "</strong><br />";
      $osTicketObj->message .= "Violated: <strong>" . ($target->{'violated'} ? 'true' : 'false') . "</strong><br />";
      $osTicketObj->message .= "-----------------------------------------------------<br />";
  }

  if ($value->{'value'}->{'message'} != "") {
    $osTicketObj->message .= "Message: <strong>" . $value->{'value'}->{'message'} . "</strong><br />";
  }

  $osTicketObj->message .= "<br /><hr /><br />";
}

$osTicketObj->message .= "Keptn Context: <strong>" . $cloudEvent->{'shkeptncontext'} . "</strong>";

$osTicketJSON = json_encode($osTicketObj);

$osTicketURL = "$osTicketURL/api/tickets.json";

/******************************
   POST DATA TO OSTICKET
******************************/
$ch = curl_init($osTicketURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $osTicketJSON);

// Set HTTP Header for POST request

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'Content-Type: application/json',
  "x-api-key: $osTicketAPIToken"
));

// Submit the POST request
$result = curl_exec($ch);

echo $result;

// Close cURL session handle
curl_close($ch);
?>
