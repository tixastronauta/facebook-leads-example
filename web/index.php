<?php session_start(); ?>
<?php require_once __DIR__ . '/../bootstrap.php'; ?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facebook Page Long-lived token</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link href="styles.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">FBauth</a>
        </div>
    </div>
</nav>

<div class="container">

    <div>
        <h1>Facebook Page Long-lived token</h1>
        <p class="lead">On this website you'll be able to connect with facebook and retrieve a <br>long-lived access token to access one of your pages.</p>
    </div>

    <div class="row">
        <form>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="appid">Facebook App Id</label>
                    <input id="appid" class="form-control" value="<?php echo $fb_app_id; ?>">
                </div>
                <div class="form-group">
                    <label for="appsecret">Facebook App Secret</label>
                    <input id="appsecret" class="form-control" value="<?php echo $fb_app_secret; ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pageid">Facebook Page Id</label>
                    <input id="pageid" class="form-control" value="<?php echo $fb_page_id; ?>">
                </div>
                <div class="form-group">
                    <label for="scope">Facebook Scope</label>
                    <input id="scope" class="form-control" value="<?php echo $fb_scope; ?>">
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-12">


            <ol>
                <li id="step1">NOT OK: <a href="#" onClick="logInWithFacebook()">Log In with the JavaScript SDK</a></li>
                <li id="step2">NOT OK: You do not have an Access Token</li>
                <li id="step3">NOT OK: Complete steps above in order to subscribe App <span class="appid"></span> to Page <span class="pageid"></span></li>
                <li id="step4">NOT OK: Complete steps above in order to get Page Access Token</li>
            </ol>

        </div>
    </div>

</div>



<script id="facebook-jssdk" src="//connect.facebook.net/en_US/sdk.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>

    var appid = $("#appid").val();
    var appsecret = $("#appsecret").val();
    var pageid = $("#pageid").val();
    var scope = $("#scope").val();

    function updateGlobalVars()
    {
        appid = $("#appid").val();
        appsecret = $("#appsecret").val();
        pageid = $("#pageid").val();
        scope = $("#scope").val();

        $(".appid").html(appid);
        $(".appsecret").html(appsecret);
        $(".pageid").html(pageid);
        $(".scope").html(scope);
    }

    function getConfigParams()
    {
        return "appid=" + appid + "&appsecret=" + appsecret + "&pageid=" + pageid + "&scope=" + scope;
    }

    $(function () {
        updateGlobalVars();

        $(document).on("change", "input", function(e) {
            updateGlobalVars();
        });

        $(document).on("keydown", "", function(e) {
            updateGlobalVars();
        });

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
            url: "get_fb_access_token.php?" + getConfigParams(),
            type: "POST",
            success: function (response) {
                console.log("get_fb_access_token response", response);
                $("#step2").html("OK: Access Token: " + response.access_token);
            }
        });
    }

    function getSubscribedApps() {
        $.ajax({
            url: "get_fb_page_subscribed_apps.php?" + getConfigParams(),
            type: "POST",
            success: function (response) {
                console.log("get_fb_page_subscribed_apps response", response);
                if (0 == response.subscribed_apps.length) {
                    $("#step3").html("NOT OK: <a href=\"#\" id=\"btn_subscribe_app\">Subscribe App " + appid + " to Page " + pageid + "</a>");
                } else {
                    var app_found = false;
                    $.each(response.subscribed_apps, function (k, v) {
                        if (v.id == appid) {
                            app_found = true;
                            return false;
                        }
                    });
                    if (false == app_found) {
                        $("#step3").html("NOT OK: Page " + pageid + " is subscribed to " + response.subscribed_apps.length + " apps but not to App " + appid + " :( <a href=\"#\" id=\"btn_subscribe_app\">Click here to Subscribe App " + appid + " to Page " + pageid + "</a>");
                    } else {
                        $("#step3").html("OK: App " + appid + " is subscribed to Page " + pageid);
                    }

                    if (response.page_access_token !== undefined)
                    {
                        $("#step4").html("OK: Page Access Token: " + response.page_access_token);
                    }


                }
            }
        });
    }

    function subscribeAppToPage() {
        $.ajax({
            url: "subscribe_app_to_page.php?" + getConfigParams(),
            type: "POST",
            success: function (response) {
                console.log("subscribe_app_to_page response", response);
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
        }, {scope: scope});
        return false;
    };
    window.fbAsyncInit = function () {

        FB.init({
            appId: appid,
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