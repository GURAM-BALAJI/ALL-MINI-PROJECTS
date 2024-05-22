<?php

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

require 'vendor/autoload.php';

$credential = new ServiceAccountCredentials(
    "https://www.googleapis.com/auth/firebase.messaging",
    json_decode(file_get_contents("pvKey.json"), true)
);

$token = $credential->fetchAuthToken(HttpHandlerFactory::build());

$ch = curl_init("https://fcm.googleapis.com/v1/projects/test-e0c2a/messages:send");

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer '.$token['access_token']
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, '{
    "message": {
      "token": "d7FqHx5cx7WIQRcirvzA_L:-YWG28uwv1s8oLQwYS0knlXDc4IpVHHsRb0vpJkgyG4LTCe-i7G94FuYvyHW10_1ekptYtu8DDtPr6nSJORWAtEh",
      "notification": {
        "title": "Background Message Title",
        "body": "Background message body",
        "image": "https://cdn.shopify.com/s/files/1/1061/1924/files/Sunglasses_Emoji.png?2976903553660223024"
      },
      "webpush": {
        "fcm_options": {
          "link": "https://google.com"
        }
      }
    }
  }');

  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "post");

  $response = curl_exec($ch);

  curl_close($ch);

  echo $response;