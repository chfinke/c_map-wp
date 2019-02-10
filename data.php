<?php
    require_once( explode( "wp-content" , __FILE__ )[0] . "wp-load.php" );
    require_once( "./helpers.php" );

    if ( user_is_moderator() ) {
        $sql = "
            SELECT *, ms.meta_value AS status, ma.meta_value AS active
                FROM $wpdb->posts p
                LEFT OUTER JOIN $wpdb->postmeta ms
                  ON    ms.post_id = p.ID
                    AND ms.meta_key='status'
                LEFT OUTER JOIN $wpdb->postmeta mp
                  ON    mp.post_id = p.ID
                    AND mp.meta_key='id_publish'
                LEFT OUTER JOIN $wpdb->postmeta ma
                  ON    ma.post_id = mp.meta_value
                    AND ma.meta_key='status'
                WHERE p.post_type='cmap'
                  AND p.post_status='draft'
                  AND ms.meta_value in ( 'unconfirmed', 'pending', 'publish' )
            ";
    } else {
        $sql = "
            SELECT *
                FROM $wpdb->posts p
                LEFT OUTER JOIN $wpdb->postmeta ms
                  ON    ms.post_id = p.ID
                    AND ms.meta_key='status'
                WHERE p.post_type='cmap'
                  AND p.post_status='publish'
                  AND ms.meta_value = 'active'
            ";
    }
    $posts = $wpdb->get_results( $sql );
    $cnt = count($posts);

    echo "{\n";
    echo "  \"type\": \"FeatureCollection\",\n";
    echo "  \"features\": [\n";

    foreach ( $posts as $post ) {
        $cnt--;
        $metas = $wpdb->get_results( "
            SELECT *
                FROM $wpdb->postmeta
                WHERE $wpdb->postmeta.post_id = $post->ID
            " );
        $meta_dict = get_meta_dict( $metas );

        echo "    {\n";
        echo "      \"geometry\": {\n";
        echo "        \"type\": \"Point\",\n";
        echo "        \"coordinates\": [\n";
        echo sprintf("          %F,\n", $meta_dict["lon"]);
        echo sprintf("          %F\n", $meta_dict["lat"]);
        echo "        ]\n";
        echo "      },\n";
        echo "      \"type\": \"Feature\",\n";
        echo "      \"properties\": {\n";
        echo sprintf("        \"id\": \"%s\",\n", $post->ID);
        echo sprintf("        \"title\": \"%s\",\n", $post->post_title);
        if ( user_is_moderator() ) {
            echo sprintf("        \"status\": \"%s\",\n", $post->status);
            echo sprintf("        \"active\": \"%s\",\n", $post->active);
            echo "        \"delta\": \"";
            echo preg_replace("/\r|\n/", "", get_delta($post->ID , $wpdb ));
            echo "\",\n";
        }
        echo sprintf("        \"address\": \"%s\"\n", $meta_dict["address"]);
        echo "      }\n";
        if ($cnt > 0)
            echo "    },\n";
        else
            echo "    }\n";
    }

    echo "  ]\n";
    echo "}\n";
?>
