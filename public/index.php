<?php

require __DIR__.'/../env.php';
require __DIR__.'/../vendor/autoload.php';

session_start();

if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="google-signin-client_id" content="<?php echo GOOGLE_CLIENT_ID; ?>">
    <title>Painel | Carteiro</title>

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
                    <li><a href="settings.php"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Configurações</a></li>
                    <li><a href="#" onclick="signOut()"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <li class="active"><a href="index.php">Painel <span class="sr-only">(current)</span></a></li>
                    <li><a href="email-create.php">Envio de e-mail</a></li>
                </ul>
            </div>

            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h1 class="page-header">Painel</h1>

                <div id="alerts"></div> <!-- /alerts -->

                <button onclick="apiLoad()">Picker</button>
                <div id="result"></div>
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
        google_api_key = '<?php echo GOOGLE_API_KEY; ?>';
        oauth_access_token = '<?php echo $_SESSION['access_token']['access_token']; ?>';
        carteiro_folder = '<?php echo $_SESSION['carteiro_folder']; ?>';

        var scope = ['https://www.googleapis.com/auth/drive'];

        var pickerApiLoaded = false;
        var oauthToken;

        function apiLoad() {
            gapi.load('auth');
            gapi.load('picker', {'callback': onPickerApiLoad});
        }

        function onPickerApiLoad() {
            pickerApiLoaded = true;
            createPicker();
        }

        // Create and render a Picker object for picking user Photos.
        function createPicker() {
            if (pickerApiLoaded && oauth_access_token) {
                var picker = new google.picker.PickerBuilder().
                addView(new google.picker.DocsView().setParent(carteiro_folder).setIncludeFolders(true)).
                setOAuthToken(oauth_access_token).
                setDeveloperKey(google_api_key).
                setCallback(pickerCallback).
                build();
                picker.setVisible(true);
            }
        }

        // A simple callback implementation.
        function pickerCallback(data) {
            var url = 'nothing';
            if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
                var doc = data[google.picker.Response.DOCUMENTS][0];
                url = doc[google.picker.Document.URL];
            }
            var message = 'You picked: ' + url;
            document.getElementById('result').innerHTML = message;
        }
    </script>
    <script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
    <script src="https://apis.google.com/js/api.js" async defer></script>
</body>
</html>
