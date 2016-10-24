<?php
$fb_api_version = 'v2.6';


$fb_scope = isset($_GET['scope']) ? $_GET['scope'] : 'manage_pages';
$fb_app_id = isset($_GET['appid']) ? $_GET['appid'] : '';
$fb_app_secret = isset($_GET['appsecret']) ? $_GET['appsecret'] : '';
$fb_page_id = isset($_GET['pageid']) ? $_GET['pageid'] : '';