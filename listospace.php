#!/usr/bin/php

<?php

// Description : Cisco Spark Messenger - To Get List Of Room / Space details
// USAGE
// CLI -> listospace.php

$SPARK_BEARER = "<your cisco spark bot authorization code aka bearer_code>";		// Head to read me file on how to obtain one
$SPARK_POSTMAN = "<your cisco spark bot authorization code aka bearer_code>";		// Not really needed

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.ciscospark.com/v1/rooms",
  CURLOPT_PROXY => "http://<username>:<password>@<proxy ip>:<proxy port>",	// PROXY
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer $SPARK_BEARER ",
    "Cache-Control: no-cache",
    "Content-Type: application/json",
    "Postman-Token: $SPARK_POSTMAN"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

$data = json_decode($response, true);
foreach ($data['items'] as $row) {
        $id = $row['id'];
        $title = $row['title'];
        $type = $row['type'];
        $isLocked = $row['isLocked'];
        $lastActivity = $row['lastActivity'];
        $creatorId = $row['creatorId'];
        $created = $row['created'];
        echo "$id  ---> $title\n";
}

?>
