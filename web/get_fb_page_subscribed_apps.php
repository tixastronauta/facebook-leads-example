<?php

session_start();

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

require_once __DIR__ . '/../bootstrap.php';


$subscribedApps = [];
$error = false;

$fb = new Facebook([
    'app_id'                => $fb_app_id,
    'app_secret'            => $fb_app_secret,
    'default_graph_version' => $fb_api_version,
]);

try
{
    /* get page access token - this token is required for operations such as publishing as a page */
    $request = $fb->request('GET', "/me/accounts", [], $_SESSION['fb_access_token']);
    $response = $fb->getClient()->sendRequest($request);
    $body = $response->getDecodedBody();
    $data = $body['data'];

    $page_access_token = null;
    foreach ($data as $account)
    {
        if ($fb_page_id == $account['id'])
        {
            $page_access_token = $account['access_token'];
            break;
        }
    }

    /* get apps subscribed to this page */
    $request = $fb->request('GET', "/{$fb_page_id}/subscribed_apps", [], $page_access_token);
    $response = $fb->getClient()->sendRequest($request);
    $body = $response->getDecodedBody();
    $subscribedApps = $body['data'];

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
    'subscribed_apps' => $subscribedApps,
    'page_access_token' => $page_access_token,
    'error'           => $error
]);
