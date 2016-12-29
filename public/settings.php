<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

session_start();

if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/login.php');
    exit;
}

$gmail_enabled = strpos($_SESSION['access_token']['scope'], Google_Service_Gmail::GMAIL_SEND) !== false;
$drive_enabled = strpos($_SESSION['access_token']['scope'], Google_Service_Drive::DRIVE) !== false;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="google-signin-client_id" content="<?php echo GOOGLE_CLIENT_ID; ?>">
    <title>Configurações | Carteiro</title>

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
                <a class="navbar-brand" href="index.php">Carteiro</a>
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
                    <li><a href="index.php">Painel</a></li>
                    <li><a href="email-create.php">Envio de e-mail</a></li>
                </ul>
            </div>

            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h1 class="page-header">Configurações</h1>

                <div id="alerts"></div> <!-- /alerts -->

                <h3>Conexão</h3>

                <div class="panel panel-default">
                    <!-- Default panel contents -->
                    <div class="panel-heading"><strong>Google</strong><button class="btn btn-danger btn-xs pull-right" title="Desconectar" onclick="revokeAccess()"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div>

                    <!-- Table -->
                    <table class="table">
                        <tr>
                            <td class="col-md-1 text-center"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span></td>
                            <td class="col-md-10">
                                Você está conectado(a) com a sua conta Google.
                            </td>
                            <td class="col-md-1 text-center">
                                <span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></td>
                            <td>
                                <span id="gmail-not-enabled-text" class="text-info <?php echo $gmail_enabled ? 'hidden' : ''; ?>">Você ainda não pode enviar e-mails.</span>
                                <span id="gmail-enabled-text" class="<?php echo !$gmail_enabled ? 'hidden' : ''; ?>">Você pode enviar e-mails.</span>
                            </td>
                            <td class="text-center">
                                <button id="gmail-not-enabled-button" class="btn btn-info btn-xs <?php echo $gmail_enabled ? 'hidden' : ''; ?>" onclick="requestGmailPermission()" title="Habilitar o envio de e-mails">Habilitar</button>
                                <span id="gmail-enabled-check" class="glyphicon glyphicon-ok text-success <?php echo !$gmail_enabled ? 'hidden' : ''; ?>" aria-hidden="true"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span></td>
                            <td>
                                <span id="drive-not-enabled-text" class="text-info <?php echo $drive_enabled ? 'hidden' : ''; ?>">Você ainda não pode armazenar arquivos.</span>
                                <span id="drive-enabled-text" class="<?php echo !$drive_enabled ? 'hidden' : ''; ?>">Você pode armazenar arquivos.</span>
                            </td>
                            <td class="text-center">
                                <button id="drive-not-enabled-button" class="btn btn-info btn-xs <?php echo $drive_enabled ? 'hidden' : ''; ?>" onclick="requestDrivePermission()" title="Habilitar o armazenamento de arquivos">Habilitar</button>
                                <span id="drive-enabled-check" class="glyphicon glyphicon-ok text-success <?php echo !$drive_enabled ? 'hidden' : ''; ?>" aria-hidden="true"></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
    <script type="text/javascript" src="js/scripts.js"></script>
    <script type="text/javascript">
        google_client_id = '<?php echo GOOGLE_CLIENT_ID; ?>';
    </script>
    <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
</body>
</html>