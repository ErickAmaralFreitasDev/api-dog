<?php 
function api_password_lost($request) {
    $login = $request['login'];
    $url = $request['url'];

    if(empty($login)) {
        $response = new WP_Error('error', 'Login é obrigatório', ['status' => 406]);
        return rest_ensure_response($response);
    }
    $user = get_user_by('email', $login);
    if(empty($user)) {
        $user = get_user_by('login', $login);
    }
    if(empty($user)) {
        $response = new WP_Error('error', 'Usuário não encontrado', ['status' => 401]);
        return rest_ensure_response($response);
    }

    $user_login = $user->user_login;
    $user_email = $user->user_email;
    $key = get_password_reset_key($user);
    
    $message = "Utilize o link abaixo para resetar sua senha:\r\n";
    $url = esc_url_raw($url . "/?key=$key&=" . rawurldecode($user_login) . "\r\n");
    $body = $message . $url;
    wp_mail($user_email, 'Redefinição de senha', $body);

    return rest_ensure_response(['message' => 'Email enviado com sucesso']);
}

function register_api_password_lost() {
    register_rest_route('api', '/password/lost', [
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'api_password_lost',
    ]);
}

add_action('rest_api_init', 'register_api_password_lost');

//password reset

function api_password_reset($request) {
    $login = $request['login'];
    $password = $request['password'];
    $key = $request['key'];

    if(empty($login) || empty($password) || empty($key)) {
        return new WP_Error('error', 'Dados incompletos', ['status' => 406]);
    }

    $user = get_user_by('login', $login);

    if(empty($user)) {
        $response = new WP_Error('error', 'Usuário não encontrado', ['status' => 401]);
        return rest_ensure_response($response);
    }

    $check_key = check_password_reset_key($key, $login);

    if(is_wp_error($check_key)) {
        $response = new WP_Error('error', 'Token inválido ou expirado', ['status' => 401]);
        return rest_ensure_response($response);
    }

    reset_password($user, $password);

    return rest_ensure_response(['message' => 'Senha alterada com sucesso']);
}

function register_api_password_reset() {
    register_rest_route('api', '/password/reset', [
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'api_password_reset',
    ]);
}

add_action('rest_api_init', 'register_api_password_reset');

?> 