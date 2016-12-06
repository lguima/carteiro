<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

session_start();

if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/login.php');
    exit;
}

$send_email_enabled = strpos($_SESSION['access_token']['scope'], Google_Service_Gmail::GMAIL_SEND) !== false;
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
    <link rel="stylesheet" type="text/css" href="css/index.css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Carteiro</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="#"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Configurações</a></li>
                    <li><a href="#" onclick="signOut()"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <li><a href="index.php">Envio de e-mail</a></li>
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h1 class="page-header">Configurações</h1>

                <div id="alerts">
                    <div id="enabled-send-email-alert" class="alert alert-success alert-dismissible hidden" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Envio habilitado!</strong> Agora você já pode começar a enviar e-mails.
                    </div>
                </div> <!-- /alerts -->

                <h3>Conexões</h3>

                <ul class="list-group">
                    <li class="list-group-item">
                        <h4 class="list-group-item-heading">Google <button class="btn btn-danger btn-sm pull-right" title="Desconectar" onclick="signOut(true)"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></h4>
                        <p class="list-group-item-text">
                            <ul>
                                <li>Você está conectado(a) com a sua conta Google.</li>
                                <li id="enable-send-email" class="<?php echo $send_email_enabled ? 'hidden' : ''; ?>"><button class="btn btn-success" onclick="requestGmailSendPermission()">Habilitar o envio de e-mails</button></li>
                                <li id="enabled-send-email" class="<?php echo !$send_email_enabled ? 'hidden' : ''; ?>">Você pode enviar e-mails.</li>
                            </ul>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/scripts.js"></script>
    <script type="text/javascript">
        oauth_client_id = '<?php echo OAUTH_CLIENT_ID; ?>';
    </script>
    <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
</body>
</html>