<?php session_start(); ?>
<?php require_once __DIR__ . '/../bootstrap.php'; ?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Facebook Login</title>
</head>
<body>
<h1>Retrieving Facebook Access Tokens and Subscribing App do Page</h1>
<ol>
    <li id="step1">NOT OK: <a href="#" onClick="logInWithFacebook()">Log In with the JavaScript SDK</a></li>
    <li id="step2">NOT OK: You do not have an Access Token</li>
    <li id="step3">NOT OK: Complete steps above in order to subscribe App <?php echo $fb_app_id; ?> to Page <?php echo $fb_page_id; ?></li>
</ol>

<script id="facebook-jssdk" src="//connect.facebook.net/en_US/sdk.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>

    $(function () {
        $(document).on("click", "#btn_get_access_token", function (e) {
            e.preventDefault();
            getAccessToken();
        });
        $(document).on("click", "#btn_subscribe_app", function (e) {
            e.preventDefault();
            subscribeAppToPage();

        });
    });

    function loggedIn() {
        $("#step1").html('OK: You are logged in & cookie is set!');
        $("#step2").html("NOT OK: Click <a href=\"#\" id=\"btn_get_access_token\">here</a> to get Access Token");
    }

    function getAccessToken() {
        $.ajax({
            url: "get_fb_access_token.php",
            type: "POST",
            success: function (response) {
                console.log(response);
                $("#step2").html("OK: Access Token: " + response.access_token);
            }
        });
    }

    function getSubscribedApps() {
        $.ajax({
            url: "get_fb_page_subscribed_apps.php",
            type: "POST",
            success: function (response) {
                console.log(response);
                if (0 == response.subscribed_apps.length) {
                    $("#step3").html("NOT OK: <a href=\"#\" id=\"btn_subscribe_app\">Subscribe App <?php echo $fb_app_id; ?> to Page <?php echo $fb_page_id; ?></a>");
                } else {
                    var app_found = false;
                    $.each(response.subscribed_apps, function (k, v) {
                        if (v.id == <?php echo $fb_app_id; ?>) {
                            app_found = true;
                            return false;
                        }
                    });
                    if (false == app_found) {
                        $("#step3").html("NOT OK: Page <?php echo $fb_page_id; ?> is subscribed to " + response.subscribed_apps.length + " apps but not to App <?php echo $fb_app_id; ?> :( <a href=\"#\" id=\"btn_subscribe_app\">Click here to Subscribe App <?php echo $fb_app_id; ?> to Page <?php echo $fb_page_id; ?></a>");
                    } else {
                        $("#step3").html("OK: App <?php echo $fb_app_id; ?> is subscribed to Page <?php echo $fb_page_id; ?>");
                    }
                }
            }
        });
    }

    function subscribeAppToPage() {
        $.ajax({
            url: "subscribe_app_to_page.php",
            type: "POST",
            success: function (response) {
                console.log(response);
                getSubscribedApps();
            }
        });
    }

    logInWithFacebook = function () {
        FB.login(function (response) {
            if (response.authResponse) {
                fbLoginSuccessCallback();
            } else {
                $("#step1").html('NOT OK: User cancelled login or did not fully authorize.');
            }
        }, {scope: 'manage_pages'});
        return false;
    };
    window.fbAsyncInit = function () {

        FB.init({
            appId: '<?php echo $fb_app_id; ?>',
            cookie: true, // This is important, it's not enabled by default
            version: '<?php echo $fb_api_version; ?>'
        });

        FB.getLoginStatus(function (response) {
            if ("connected" == response.status) {
                fbLoginSuccessCallback();
            }
        });
    };

    function fbLoginSuccessCallback() {
        loggedIn();
        getAccessToken();
        getSubscribedApps();
    }

</script>

</body>
</html>