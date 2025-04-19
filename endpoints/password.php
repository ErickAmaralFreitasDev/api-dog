<?php 
function api_password_lost($request) {
    $login = $request['login'];
    $url = $request['url'];

    if(empty($login)) {
        return new WP_Error('error', 'Login é obrigatório', ['status' => 406]);
    }

    $user = get_user_by('email', $login);
    if(empty($user)) {
        $user = get_user_by('login', $login);
    }
    if(empty($user)) {
        return new WP_Error('error', 'Usuário não encontrado', ['status' => 401]);
    }

    $user_login = $user->user_login;
    $user_email = $user->user_email;
    $key = get_password_reset_key($user);

    // Corrigindo a construção da URL
    $reset_url = esc_url_raw($url . "/?key=$key&login=" . rawurlencode($user_login));
    
    $message = "Utilize o link abaixo para resetar sua senha:\r\n";
    $message .= $reset_url;

    // Corrigindo a função de envio de email
    $email_sent = wp_mail(
        $user_email,
        'Recuperação de senha',
        $message
    );

    if(!$email_sent) {
        return new WP_Error('error', 'Falha ao enviar email', ['status' => 500]);
    }

    return rest_ensure_response(['message' => 'Email enviado com sucesso']);
}

function register_api_password_lost() {
    register_rest_route('api', '/password/lost', [
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'api_password_lost',
    ]);
}

add_action('rest_api_init', 'register_api_password_lost');
?>