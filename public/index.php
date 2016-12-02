<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

session_start();

if (!isset($_SESSION['google_sub']) || empty($_SESSION['google_sub']))
    header('Location: http://'. $_SERVER['HTTP_HOST'] .'/login.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="google-signin-client_id" content="200909192167-qck6j2hh0kma3cg1h95p6n2k1e1o3jqd.apps.googleusercontent.com">
    <title>Carteiro - Para quem quer evitar a fadiga</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <h1>Carteiro</h1>

    <a href="#" onclick="signOut();">Sign out</a>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript">
        function init() {
            gapi.load('auth2', function() {
                auth2 = gapi.auth2.init({
                    client_id: '200909192167-qck6j2hh0kma3cg1h95p6n2k1e1o3jqd.apps.googleusercontent.com',
                    fetch_basic_profile: false,
                    scope: 'openid'
                });
            });
        }

        function signOut() {
            var auth2 = gapi.auth2.getAuthInstance();
            var id_token = auth2.currentUser.get().getAuthResponse().id_token;

            $.post('sign-out.php', {"id_token": id_token}, function(data) {
                if (data.id_token && data.id_token == id_token) {
                    auth2.signOut().then(function() {
                        window.location.replace('http://<?php echo $_SERVER['HTTP_HOST']; ?>/login.php');
                    });
                }
            }, 'json');
        }
    </script>
    <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
</body>
</html>