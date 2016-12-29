var google_client_id = '';
var granted_addicional_permission = false;

function init() {
    gapi.load('auth2', function() {
        auth2 = gapi.auth2.init({
            client_id: google_client_id,
            fetch_basic_profile: false,
            scope: 'openid'
        });

        auth2.currentUser.listen(userListener);
    });
}

function renderButtonLogin() {
    gapi.signin2.render('carteiro-signin', {
        width: 240,
        height: 50,
        longtitle: true,
        theme: 'dark',
        onsuccess: onLoginSuccess,
        onfailure: onLoginFailure
    });
}

function initLogin() {
    init();
    renderButtonLogin();
}

function userListener(googleUser) {
    if (!granted_addicional_permission)
        return false;

    var auth_response = googleUser.getAuthResponse();
    var id_token = auth_response.id_token;

    $.post('http://'+ window.location.host +'/sign-in.php', {"auth_response": auth_response}, null, 'json');

    granted_addicional_permission = false;
}

function onLoginSuccess(googleUser) {
    signIn(googleUser.getAuthResponse());
}

function signIn(auth_response) {
    var id_token = auth_response.id_token;

    $.post('http://'+ window.location.host +'/sign-in.php', {"auth_response": auth_response}, function(data) {
        if (data.id_token && data.id_token == id_token)
            window.location.replace('http://'+ window.location.host +'/index.php');
    }, 'json');
}

function onLoginFailure(error) {
    if (error.type == 'tokenFailed' && !$('#login-access-denied').length) {
        $('#alerts').prepend('<div id="login-access-denied" class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Pode confiar!</strong> Esta permissão não dá acesso a nenhum dado seu.</div>');
    }
}

function signOut(revoke_token = false) {
    if (revoke_token)
        auth2.disconnect();

    var id_token = auth2.currentUser.get().getAuthResponse().id_token;

    $.post('http://'+ window.location.host +'/sign-out.php', {"id_token": id_token, "revoke_token": revoke_token}, function(data) {
        if (data.id_token && data.id_token == id_token) {
            auth2.signOut().then(function() {
                window.location.replace('http://'+ window.location.host +'/login.php');
            });
        }
    }, 'json');
}

function revokeAccess() {
    bootbox.confirm({
        backdrop: true,
        title : "Desconectar",
        message: "<p>Sem a sua conta Google não é possível utilizar o Carteiro.</p><p><b>Tem certeza que deseja desconectar?</b></p>",
        buttons: {
            confirm: {
                label: 'Sim, desconectar',
                className: 'btn-danger'
            },
            cancel: {
                label: 'Cancelar'
            }
        },
        callback: function (confirmed) {
            if (confirmed)
                signOut(true);
        }
    });
}

/* Gmail */
function requestGmailPermission() {
    googleUser = auth2.currentUser.get();

    googleUser.grant({
        scope: 'https://www.googleapis.com/auth/gmail.send'
    }).then(onGmailPermissionSuccess, onGmailPermissionFailure);
}

function onGmailPermissionSuccess() {
    granted_addicional_permission = true;

    if ($('#gmail-denied-alert').length)
        $('#gmail-access-denied').remove();

    $('#gmail-not-enabled-button').fadeOut(function(){
        $('#gmail-enabled-check').hide().removeClass('hidden').fadeIn();
    });

    $('#gmail-not-enabled-text').fadeOut(function(){
        $('#gmail-enabled-text').hide().removeClass('hidden').fadeIn();
    });
}

function onGmailPermissionFailure(error) {
    if (error.type == 'tokenFailed' && !$('#gmail-access-denied').length)
        $('#alerts').prepend('<div id="gmail-access-denied" class="alert alert-info alert-dismissible access-denied-alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Não se preocupe!</strong> Somente enviaremos e-mails que você solicitar.</div>');
}

/* Google Drive */
function requestDrivePermission() {
    googleUser = auth2.currentUser.get();

    googleUser.grant({
        scope: 'https://www.googleapis.com/auth/drive'
    }).then(onDrivePermissionSuccess, onDrivePermissionFailure);
}

function onDrivePermissionSuccess() {
    granted_addicional_permission = true;

    if ($('#drive-access-denied').length)
        $('#drive-access-denied').remove();

    $('#drive-not-enabled-button').fadeOut(function(){
        $('#drive-enabled-check').hide().removeClass('hidden').fadeIn();
    });

    $('#drive-not-enabled-text').fadeOut(function(){
        $('#drive-enabled-text').hide().removeClass('hidden').fadeIn();
    });
}

function onDrivePermissionFailure(error) {
    if (error.type == 'tokenFailed' && !$('#drive-access-denied').length)
        $('#alerts').prepend('<div id="drive-access-denied" class="alert alert-info alert-dismissible access-denied-alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Fique tranquilo(a)!</strong> Armazenaremos apenas os arquivos necessários para enviar seus e-mails.</div>');
}
