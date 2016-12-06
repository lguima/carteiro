var oauth_client_id = '';
var granted_addicional_permission = false;

function init() {
    gapi.load('auth2', function() {
        auth2 = gapi.auth2.init({
            client_id: oauth_client_id,
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
    if (error.type == 'tokenFailed' && !$('#alerts #access-denied').length) {
        $('#alerts').append('<div id="access-denied" class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Pode confiar!</strong> Esta permissão não dá acesso a nenhum dado seu.</div>');
    }
}

function signOut(revoke_token = false) {
    var id_token = auth2.currentUser.get().getAuthResponse().id_token;

    $.post('http://'+ window.location.host +'/sign-out.php', {"id_token": id_token, "revoke_token": revoke_token}, function(data) {
        if (data.id_token && data.id_token == id_token) {
            auth2.signOut().then(function() {
                window.location.replace('http://'+ window.location.host +'/login.php');
            });
        }
    }, 'json');
}

function requestGmailSendPermission() {
    googleUser = auth2.currentUser.get();

    googleUser.grant({
        scope: 'https://www.googleapis.com/auth/gmail.send'
    }).then(onPermissionSuccess, onPermissionFailure);
}

function onPermissionSuccess(auth_response) {
    granted_addicional_permission = true;

    if ($('#alerts #access-denied').length)
        $('#access-denied').remove();

    $('#enable-send-email').remove();
    $('#enabled-send-email').removeClass('hidden');
    $('#enabled-send-email-alert').removeClass('hidden');
}

function onPermissionFailure(error) {
    if (error.type == 'tokenFailed' && !$('#alerts #access-denied').length)
        $('#alerts').append('<div id="access-denied" class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Pode confiar!</strong> Somente enviaremos e-mails que você solicitar.</div>');
}