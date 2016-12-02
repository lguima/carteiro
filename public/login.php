<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

session_start();

if (isset($_SESSION['google_sub']) && !empty($_SESSION['google_sub']))
    header('Location: http://'. $_SERVER['HTTP_HOST'] .'/index.php');

$signed_out = (isset($_SESSION['signed_out']) && $_SESSION['signed_out']);

unset($_SESSION['signed_out']);
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
    <style type="text/css">
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #FFF;
        }
        .signin {
            max-width: 308px;
            padding: 15px;
            margin: 0 auto;
            margin-bottom: 10px;
            text-align: center;
        }
        #carteiro-signin {
            margin-top: 50px;
        }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="container">
        <div id="access-denied" class="alert alert-success alert-dismissible <?php echo $signed_out ? '' : 'hide' ; ?>" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Pronto!</strong> Até logo!
        </div>

        <div id="access-denied" class="alert alert-info alert-dismissible hide" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Pode confiar!</strong> Nenhum dado seu será acessado.
        </div>

        <div class="row signin">
            <div class="col-md-12">
                <h1 class="form-signin-heading">Carteiro</h1>
                <h4 class="form-signin-heading">Para quem quer evitar a fadiga</h4>
                <p>
                    <img src="images/carteiro.png" alt="Carteiro" width="240" />
                </p>
                <div id="carteiro-signin"></div>
            </div>
        </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript">
        function onSuccess(googleUser) {
            var id_token = googleUser.getAuthResponse().id_token;

            $.post('sign-in.php', {"id_token": id_token}, function(data) {
                if (data.id_token && data.id_token == id_token)
                    window.location.replace('http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php');
            }, 'json');
        }

        function onFailure(error) {
            if (error.type == 'tokenFailed')
                $('#access-denied').removeClass('hide');
        }

        function init() {
            gapi.load('auth2', function() {
                auth2 = gapi.auth2.init({
                    client_id: '200909192167-qck6j2hh0kma3cg1h95p6n2k1e1o3jqd.apps.googleusercontent.com',
                    fetch_basic_profile: false,
                    scope: 'openid'
                });
            });

            gapi.signin2.render('carteiro-signin', {
                'width': 240,
                'height': 50,
                'longtitle': true,
                'theme': 'dark',
                'onsuccess': onSuccess,
                'onfailure': onFailure
            });
        }
    </script>
    <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
</body>
</html>