<?php

function photo_data ($post) {
    $post_meta = get_post_meta($post->ID);
    $img_id = isset($post_meta['img'][0]) ? $post_meta['img'][0] : '';
    $src = '';
    
    if (!empty($img_id)) {
        $image_data = wp_get_attachment_image_src($img_id, 'large');
        if ($image_data) {
            $src = $image_data[0];
        }
    }
    $user = get_userdata($post->post_author);
    $total_comments = get_comments_number($post->ID);

    $src_fallback = '';
    if (isset($post_meta['img'][0])) {
        $image_data = wp_get_attachment_image_src($post_meta['img'][0], 'large');
        $src_fallback = $image_data ? $image_data[0] : '';
    }

    return [
        'id' => $post->ID,
        'author' => $user ? $user->user_login : 'Anônimo',
        'title' => $post->post_title,
        'date' => date('d/m/Y H:i', strtotime($post->post_date)),
        'src' => $src_fallback,
        'peso' => $post_meta['peso'][0] ?? 'Não informado',
        'idade' => $post_meta['idade'][0] ?? 'Não informado',
        'acessos' => $post_meta['acessos'][0] ?? 0,
        'total_comments' => get_comments_number($post->ID)
    ];
}

function api_photo_get($request) {
    $post_id = (int) $request['id'];
    $post = get_post($post_id);

    if (!isset($post) || empty($post)) {
        $response = new WP_Error('error', 'Post não encontrado', ['status' => 404]);
        return rest_ensure_response($response);
    }

    $photo = photo_data($post);
    $photo['acessos'] = (int) $photo['acessos'] + 1;
    update_post_meta($post_id, 'acessos', $photo['acessos']);

    $comments = get_comments([
        'post_id' => $post_id,
        'order' => 'ASC'
    ]);

    $response = [
        'photo' => $photo,
        'comments' => $comments,
    ];

    return rest_ensure_response($response); 
}

function register_api_photo_get() {
    register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_photo_get',
    ]);
}

add_action('rest_api_init', 'register_api_photo_get');

function api_photos_get($request) {
    $_total = sanitize_text_field($request['_total']) ?: 6;
    $_page = sanitize_text_field($request['_page']) ?: 1;
    $_user = sanitize_text_field($request['_user']) ?: 0 ;

    if(!is_numeric($_user)) {
        $user = get_user_by('login', $_user);
            if (!$user) {
                $response = new WP_Error('error', 'Usuário não encontrado', ['status' => 404]);
                return rest_ensure_response($response);
            }
        $_user = $user->ID;
    }

    $args = [
        'post_type' => 'post',
        'author' => $_user,
        'posts_per_page' => $_total,
        'paged' => $_page,
    ];

    $query = new WP_Query($args);
    $posts = $query->posts;

    $photos = [];
        if ($posts) {
            foreach ($posts as $post) {
                $photos[]= photo_data($post);
            }
        }

    // $response = [
    //     'total' => $_total,
    //     'page' => $_page,
    //     'user' => $_user,
    // ];
    
    return rest_ensure_response($photos); 
}

function register_api_photos_get() {
    register_rest_route('api', '/photo', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_photos_get',
        'permission_callback' => '__return_true' 
    ]);
}

add_action('rest_api_init', 'register_api_photos_get');
?>