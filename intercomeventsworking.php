<?php

ini_set('memory_limit', '-1');

// Construct user ID array
$user_IDs = array();

//Construct an events array per user
$events = array();

// Helper function to build a data array from json, getting only the user_IDs you need
function returnIDs($data, $user_IDs) {
  foreach ($data->users as $user) {
    //push the intercom_user_id to the array
    array_push($user_IDs, $user->id);
  }
  return $user_IDs;
}

// Helper function to build a data array from json, getting only the events.
function returnEvents($data, $events) {
  foreach ($data->events as $event) {
    // create a new array of the user_id and event_name and push to the events array
    array_push($events, [$event->user_id => $event->event_name]);
  }
  return $events;
}

// A funtion that recusrviely calls itself until the
// "next" page attribute is falsey
function exhaustIDPages($url, $user_IDs) {
  // Set curl variables
  $login    = 'q2f7lfq7';
  $password = 'ro-b846151c4e64b0db9ab9bd3d427cc4ab485c8470';
  $headers  = array(
    'Accept: application/json',
    'Content-Type: application/json'//,
  );

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($ch, CURLOPT_USERPWD, $login . ":" . $password);

  //Execute curl & convert to valid object
  $result = curl_exec($ch);
  $json = json_decode($result);

  // Add this page's ID's to our array (will add
  // subsequent pages from the recursive call also)
  $user_IDs = returnIDs($json, $user_IDs);

  print_r("Got " . count($user_IDs) . " users\r\n");

  // Close the current curl connection
  curl_close($ch);

  // Here's where the recursion comes in. While the
  // next page has a truthy value, call exhaustPages again
  while($json->pages->next) {
    // set user_IDs array to the value of the array returned from
    // the recursive call. The URl here is changing all the time
    $user_IDs = exhaustIDPages($json->pages->next, $user_IDs);
    // Set the current instance of 'next page' to false, to
    // prevent a stack overflow from occuring
    $json->pages->next = false;
  }

  // finally, after the while loop ends, return the final
  // array constructed
  return $user_IDs;

}

function exhaustEventPages($url, $events) {
  // Set curl variables
  $login    = 'q2f7lfq7';
  $password = 'ro-b846151c4e64b0db9ab9bd3d427cc4ab485c8470';
  $headers  = array(
    'Accept: application/json',
    'Content-Type: application/json'//,
  );

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($ch, CURLOPT_USERPWD, $login . ":" . $password);

  //Execute curl & convert to valid object
  $result = curl_exec($ch);
  $json   = json_decode($result);

  // Add this page's ID's to our array (will add
  // subsequent pages from the recursive call also)
  $events= returnEvents($json, $events);

  print_r("Got " . count($events) . " events\r\n");

  // Close the current curl connection
  curl_close($ch);

  // Here's where the recursion comes in. While the
  // next page has a truthy value, call exhaustPages again
  while($json->pages->next) {
    // set user_IDs array to the value of the array returned from
    // the recursive call. The URl here is changing all the time
    $events = exhaustEventPages($json->pages->next, $events);
    // Set the current instance of 'next page' to false, to
    // prevent a stack overflow from occuring
    $json->pages->next = false;
  }

  // finally, after the while loop ends, return the final
  // array constructed
  return $events;

}

print_r("Starting to get users...");
$user_IDs = exhaustIDPages('https://api.intercom.io/users', $user_IDs);
print_r("Finished users, found " . count($user_IDs) . "\r\n");
print_r("Going to get events now...");
for($i=0;$i<count($user_IDs);$i++){
  $events = exhaustEventPages('https://api.intercom.io/events?type=user&intercom_user_id=' . $user_IDs[$i], $events);
}
print_r("Finished events, found " . count($events) . " total events \r\n");
print_r($events);

?>
