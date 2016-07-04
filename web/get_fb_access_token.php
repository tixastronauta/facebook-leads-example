<?php

session_start();

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

require_once __DIR__ . '/../bootstrap.php';


$accessToken = null;
$error = false;

$fb = new Facebook([
    'app_id'                => $fb_app_id,
    'app_secret'            => $fb_app_secret,
    'default_graph_version' => $fb_api_version,
]);

$client = $fb->getOAuth2Client();
$helper = $fb->getJavaScriptHelper();

try
{
    $shortLivedTokenObject = $helper->getAccessToken(); /* this token expires */
    error_log("\$shortLivedTokenObject: {$shortLivedTokenObject}");
    $accessTokenObject = $client->getLongLivedAccessToken($shortLivedTokenObject); /* this is a never-expiring token - it should NEVER be exposed on client side */

    $accessToken = $accessTokenObject->getValue();
    error_log("\$accessToken: {$accessToken}");

    $_SESSION['fb_access_token'] = $accessToken;
} catch (FacebookResponseException $e)
{
    // When Graph returns an error
    $error = 'Graph returned an error: ' . $e->getMessage();
} catch (FacebookSDKException $e)
{
    // When validation fails or other local issues
    $error = 'Facebook SDK returned an error: ' . $e->getMessage();
}


header('Content-Type: application/json');
echo json_encode([
    'access_token'            => $accessToken,
    'error'                   => $error
]);
