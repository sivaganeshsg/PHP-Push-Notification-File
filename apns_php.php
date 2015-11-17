<?php
/*

- Make changes to 1, 2, 3

- To verify the APNS Certificate : Use the following command :

Command : 
openssl s_client -connect gateway.sandbox.push.apple.com:2195 -cert apns_cert.pem -key apns_cert.pem

Note :
* Replace 'apns_cert' with certificate name (two places in the command)
* It's OKAY to get "unable to get local issuer certificate" error as long as it gets Connected. See "Verify_apns_certificate.png"

*/

ini_set('display_errors','On'); 
error_reporting(E_ALL);
// Change 1 : No braces and no spaces
$deviceToken= ''; 
// Change 2 : If any
$passphrase = ''; 
$message = 'my push notification';
$ctx = stream_context_create();
// Change 3 : APNS Cert File name and location.
stream_context_set_option($ctx, 'ssl', 'local_cert', 'apns_cert.pem'); 
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
// Open a connection to the APNS server
$fp = stream_socket_client(
    'ssl://gateway.sandbox.push.apple.com:2195', $err,
    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
if (!$fp)
    exit("Failed to connect: $err $errstr" . PHP_EOL);
echo 'Connected to APNS' . PHP_EOL;
// Create the payload body
$body['aps'] = array(
    'alert' => $message,
    'sound' => 'default'
    );

// Encode the payload as JSON
$payload = json_encode($body);
// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
if (!$result)
    echo 'Message not delivered' . PHP_EOL;
else
    echo 'Message successfully delivered' . PHP_EOL;
// Close the connection to the server
fclose($fp);

?>