#!/usr/bin/php

<?php

// Cisco Spark Messenger
// Note : added authorization code
//
// USAGE
// CLI -> messenger.php <space id / room id> "message"
// messenger.php 644teuehdshSJSJDaq9828738343hHFHss authcode "test"
// URL -> curl -G --insecure "https://localhost/messenger.php" --data-urlencode "argument1=<spaceid>" --data-urlencode "argument2=<authcode>" --data-urlencode "messages"

// TO DISABLE SPARK MESSAGE
// 0 = disable
// 1 = enable

$SPARK_ACTIVE = "1";		// ON/OFF SWITCH
$SPARK_URL = "https://api.ciscospark.com/v1/messages";		// RESTAPI URL
$PROXY_URL = "http://<username>:<password>@<proxy ip>:<proxy port>";		// PROXY
$SPARK_BEARER = "<your cisco spark bot authorization code aka bearer_code>";		// Head to read me file on how to obtain one
$SPARK_POSTMAN = "<your cisco spark bot authorization code aka bearer_code>";		// Not really needed
$LOGFILE="/tmp/ciscospark.log";		// your log location
$AUTHCFG = '/var/www/html/ciscospark/auth.cfg';


$date = date_create();
$timestamp = date_timestamp_get($date);

// To managae both CLI and URL method
if (PHP_SAPI === 'cli') {
        $SPARK_ROOMID = $argv[1];
        $SPARK_AUTHCODE = $argv[2];
        $SPARK_MSG = $argv[3];
}
else {
        $SPARK_ROOMID = $_GET['argument1'];
        $SPARK_AUTHCODE = $_GET['argument2'];
        $SPARK_MSG = $_GET['argument3'];
}


// To validate enable status
if ($SPARK_ACTIVE === '0') {
        $message = "Cisco Spark Messenger has been disabled";
        echo $message;
        $myfile = fopen("$LOGFILE", "a") or die("Unable to open file!");
        $txt = "$timestamp | $message";
        fwrite($myfile, "\n". $txt);
        fclose($myfile);
        exit(1);

}
else {
        $message = "Cisco Spark Messenger is active\n";
        echo $message;
}

// Lets Authorise the sender
$searchfor = "$SPARK_AUTHCODE";
$contents = file_get_contents($AUTHCFG);

if(preg_match("~\b$searchfor\b~",$contents)){
        echo "You are authorized:\n";
}
else{
        echo "Sorry invalid auth code. Please contact the administrator\n";
        echo "Code:$searchfor\n";
        exit(1);
}



// Send Module
$curl = curl_init();
curl_setopt_array($curl, array(
        CURLOPT_URL => "$SPARK_URL",
        CURLOPT_PROXY => "$PROXY_URL",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\r\n    \"roomId\": \"$SPARK_ROOMID\",\r\n\t\"text\": \"$SPARK_MSG\"\r\n}",
        CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $SPARK_BEARER",
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "Postman-Token: $SPARK_POSTMAN"
        ),
));


$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
                echo "cURL Error #:" . $err;
        }
        else {
                echo $response;
                $myfile = fopen("$LOGFILE", "a") or die("Unable to open file!");
                $txt = "$timestamp | $SPARK_ROOMID | $SPARK_AUTHCODE | $response";
                fwrite($myfile, "\n". $txt);
                fclose($myfile);
        }

?>
