<?php

session_start();

use Facebook\Facebook;
use Facebook\FacebookRequest;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

require_once __DIR__ . '/../bootstrap.php';


$fb = new Facebook([
    'app_id'                => $fb_app_id,
    'app_secret'            => $fb_app_secret,
    'default_graph_version' => $fb_api_version,
]);


$subscribedApps = [];

try
{
    /* get page access token */
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


    /* subscribed app to this page */
    $request = $fb->request('POST', "/{$fb_page_id}/subscribed_apps", [], $page_access_token);
    $response = $fb->getClient()->sendRequest($request);
    $body = $response->getDecodedBody();
    error_log(print_r($body, true));

} catch (FacebookResponseException $e)
{
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (FacebookSDKException $e)
{
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}


header('Content-Type: application/json');
echo json_encode([
    // @todo
//    'subscribed_apps' => $subscribedApps

]);
