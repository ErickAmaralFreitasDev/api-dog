<?php 
function api_photo_post($request) {
    $user = wp_get_current_user();
    $user_id = $user -> ID;

    if (user_id === 0) {
        $response = new WP_Error('error', 'Usuário não possui permissão', [status => 401]);
        return rest_ensure_response($response);
    }

    $nome = sanitize_text_field($request['nome']);
    $peso = sanitize_text_field($request['peso']);
    $idade = sanitize_text_field($request['idade']);

    if (empty($nome) || empty($peso) || empty($idade)) {
        $response = new WP_Error('error', 'Dados incompletos', [status => 422]);
        return rest_ensure_response($response); 
    }

    return rest_ensure_response(3);
}

function register_api_photo_post() {
    register_rest_route('api', '/photo', [
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'api_photo_post',
    ]);
}

add_action('rest_api_init', 'register_api_photo_post');
?>