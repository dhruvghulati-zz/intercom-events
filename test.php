<?php
//Tests that your script is working for a sample User ID
$login    = 'insert your account id here';
$password = 'insert your API key here';
$headers  = array(
  'Accept: application/json',
  'Content-Type: application/json'//,
);
header('Content-Type: application/json');
$url = 'https://api.intercom.io/events?type=user&user_id=1211';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $login . ":" . $password);

//Execute curl & convert to valid object
$result = curl_exec($ch);

$json = json_decode($result);

$events = array();

foreach ($json->events as $event) {
  if(isset($event->event_name) && (($event->event_name == 'clicked export') || ($event->event_name == 'clicked follow')
  || ($event->event_name == 'clicked unfollow')|| ($event->event_name == 'created a report')|| ($event->event_name == 'clicked create report')
  || ($event->event_name == 'clicked view more connected sources')|| ($event->event_name == 'artist followed')|| ($event->event_name == 'unfollowed artist')|| ($event->event_name == 'any authentication'))) {
  array_push($events, [$event->user_id => $event->event_name]);
  }
// clicked edit sources


}

print_r($events);


// function to_csv( $array ) {
//  $csv;
//
//  ## Grab the first element to build the header
//  $arr = array_pop( $array );
//  $temp = array();
//  foreach( $arr as $key => $data ) {
//    $temp[] = $key;
//  }
//  $csv = implode( ',', $temp ) . "n";
//
//  ## Add the data from the first element
//  $csv .= to_csv_line( $arr );
//
//  ## Add the data for the rest
//  foreach( $array as $arr ) {
//    $csv .= to_csv_line( $arr );
//  }
//
//  return $csv;
// }
//
// function to_csv_line( $array ) {
//  $temp = array();
//  foreach( $array as $elt ) {
//    $temp[] = '"' . addslashes( $elt ) . '"';
//  }
//
//  $string = implode( ',', $temp ) . "n";
//
//  return $string;
// }

// to_csv($events);

$fp = fopen('events.csv', 'w+');
$header = array('User_ID',",",'Event');
fputcsv ($fp, $header, "\t");

foreach ($events as $event) {
    $line = array(key($event),',',$event[key($event)]);
    fputcsv($fp, $line,"\t");
}

fclose($fp);


curl_close($ch);
?>
