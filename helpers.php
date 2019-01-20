<?php

function str_rand(int $length = 64){
    $length = ($length < 4) ? 4 : $length;
    return bin2hex(random_bytes(($length-($length%2))/2));
}

function check_token($id, $token){
    $token_expected = get_post_meta($id,"token",true);
    if (!isset($token_expected) or $token_expected == "")
        return false;
    if ($token != $token_expected)
        return false;
    return true;
}

function set_token($id) {
    $token = str_rand();
    update_post_meta($id , 'token', $token );
    return $token;
}

function get_meta_dict( $metas ) {
    $dict = array();
    if (is_array( $metas ) ) {
        foreach ( $metas as $meta ) {
            $dict[$meta->meta_key] = $meta->meta_value;
        }
    }
    return $dict;
}

function user_is_moderator() {
    return current_user_can('administrator');
}

function publish($id) {
    if ( !user_is_moderator() || get_post_meta( $id, 'status', true) != "pending" ) {
        exit();
    }
    
    update_post_meta( $id, 'status', "publish" );
    $id_publish = get_post_meta( $id, 'id_publish', true);
    if ( !isset($id_publish) || $id_publish =="") {
        $id_publish = wp_insert_post(
            array(
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
                'post_author'    => wp_get_current_user(),
                'post_name'	     => '',
                'post_title'     => '',
                'post_status'    => 'publish',
                'post_type'      => 'crowdmap',
            )
        );
        update_post_meta( $id, 'id_publish', $id_publish);
        update_post_meta( $id_publish, 'status', "active");
    }
    $post_draft = get_post( $id );       
    $post_publish = get_post( $id_publish );       
    $post_publish->post_title = $post_draft->post_title;
    wp_insert_post($post_publish);
    update_post_meta( $id_publish, 'address', get_post_meta( $id, 'address', true) );
    update_post_meta( $id_publish, 'lat', get_post_meta( $id, 'lat', true) );
    update_post_meta( $id_publish, 'lon', get_post_meta( $id, 'lon', true) ); 
    // TODO custom copy meta
    
    if (isset($_POST["id"])) {
        if ( $_POST["active"] == "1") {
            update_post_meta( $id_publish, 'status', "active" );
        } else {
            update_post_meta( $id_publish, 'status', "inactive" );
        }
    }
}

?>
