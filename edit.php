<?php
    require_once( explode( "wp-content" , __FILE__ )[0] . "wp-load.php" );
    require_once( "./helpers.php" );
    get_header();
?>
<div style="padding-left: 1em; padding-right: 1em;">

<?php
    $id = $_GET["id"];
    $action = $_GET["action"];
    
    if (!isset($id) && !isset($action)) {
        // edit
?>
    <h2 class="page-title">Eigene Karteneinträge</h2>
<?php
        $email = "";
        if (isset($_GET["email"]))  $email = $_GET["email"];
        if (isset($_POST["email"])) $email = $_POST["email"];
        if ($email == "") {
?>
    Links zu den eigenen Einträgen werden per E-Mail verschickt:
    <form name="data" method="POST" onsubmit="return form_validation()" action="<?php echo get_bloginfo('wpurl'); ?>/cmap/edit">
        <input type="text" id="email" name="email"/><br />    
        <input type="submit" value="Anfragen"/>
        <input type="button" value="Zurück" onclick="window.history.back();"/>
    </form>

    <script type="text/javascript">
            
        function form_validation() {
            var email = document.forms["data"]["email"].value;
            if (email == "" || email == null) {
                return false;
            }
    </script>

<?php
        } else {
            $posts = $wpdb->get_results( "
                SELECT distinct p.post_title, p.ID                
                    FROM $wpdb->posts p
                    INNER JOIN $wpdb->postmeta ms 
                      ON    ms.post_id = p.ID
                        AND ms.meta_key='status'
                    INNER JOIN $wpdb->postmeta mm 
                      ON    mm.post_id = p.ID
                        AND mm.meta_key='email'
                    WHERE p.post_type='cmap'
                      AND post_status='draft'
                      AND ms.meta_value in ('unconfirmed', 'pending', 'publish')
                      AND mm.meta_value='".$email."'
                " );
            $cnt = count($posts);
            
            if ($cnt>0){
                $subject = 'Deine Karteneinträge auf '.get_bloginfo('name');
                $body = 'Hallo,<br/>du hast folgende Karteneinträge auf '.get_bloginfo('name').' gemacht.<br/>Zum Ändern bitte folgende Links anklicken:<ul>';
                foreach ( $posts as $post ) {
                    $token = set_token($post->ID);
                    $body .= "<li><a href=\"".get_bloginfo('wpurl')."/cmap/edit?id=".$post->ID."&token=".$token."\">".$post->post_title."</a></li>";
                }
                $body .= '</ul>';
                $headers = array('Content-Type: text/html; charset=UTF-8');
         
                wp_mail( $email, $subject, $body, $headers );
            }
?>
    <p>E-Mail wurde verschickt.</p>
    <input type="button" value="Zurück" onclick="window.history.go(-2);"/>
<?php
        }
    } elseif (isset($id) || $action == 'new') {
        // edit?id=123
        // edit?action=new
?>
    <h2 class="page-title">Karteneintrag bearbeiten</h2>

<?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $status = 'edit';
            $post = get_post( $id );
            
            $title = $post->post_title;
            $address = get_post_meta($id,"address",true);
            $lat = get_post_meta($id,"lat",true);
            $lon = get_post_meta($id,"lon",true);
            $token = $_GET["token"];
        } else {
            $id = -1;
            $status = 'create';
            $title = '';
            $address = '';
            $lat = '0';
            $lon = '0';
        }

        if ( (isset($status)&&$status == 'create') || user_is_moderator() || ( check_token($id, $token) && get_post_meta( $id, 'status', true) != "unconfirmed" ) ) {
?>
    <form name="data" method="POST" onsubmit="return form_validation()" action="<?php echo get_bloginfo('wpurl'); ?>/cmap/functions?action=save">
        <input type="hidden" name="id" value="<?php echo $id ?>">
<?php
            if ($status == 'edit') {
?>
        <input type="hidden" name="token" value="<?php echo $token ?>">
<?php
            }
?>
        Beschreibung: <input required="yes" type="text" id="title" name="title" value="<?php echo $title ?>" /><br />
        Adresse: <input required="yes" type="text" id="address" name="address" value="<?php echo $address ?>" /><br />
        
        Position:
        <iframe src="<?php echo get_bloginfo('wpurl'); ?>/cmap/edit_map?lat=<?php echo $lat ?>&lon=<?php echo $lon ?>" height="350px" width="100%"></iframe>
        
        <input type="hidden" id="lat" name="lat" value="<?php echo $lat ?>" />
        <input type="hidden" id="lon" name="lon" value="<?php echo $lon ?>" />

<?php            
            if (isset($status)&&$status == 'create') {
?>
        E-Mail: <input required="yes" type="text" id="email" name="email"/><br />
<?php
            }    
            if( user_is_moderator()) {
                if( get_post_meta($id,"status",true) == "unconfirmed" ) {
?>
         <input type="checkbox" name="confirm" value="1">E-Mailaddresse bestätigen<br>
<?php
                }
                if( get_post_meta($id,"status",true) == "unconfirmed" || get_post_meta($id,"status",true) == "pending" ) {
?>
         <input type="checkbox" name="publish" value="1">Eintrag veröffentlichen<br>
<?php
                }
                $publish_status = get_post_meta(get_post_meta($id,"id_publish",true),"status",true);
                if( $publish_status == "active" ) {
?>
         <input type="checkbox" name="active" value="1" checked>Eintrag anzeigen<br>
<?php
                } else {
?>
         <input type="checkbox" name="active" value="1">Eintrag anzeigen<br>
<?php
                }
?>
        <br/>
        <input type="submit" value="Ändern"/>
<?php        
            } else {
?>
<?php
                if (isset($status)&&$status == 'edit') {
                    if( get_post_meta($id,"status",true) != "publish" ) {
?>
        <p>Die bisherigen Änderungen wurden noch nicht freigeschaltet.</p>
<?php
                    }
?>
        <input type="submit" value="Ändern"/>
        <input type="button" value="Löschen" onclick="window.location.href='<?php echo get_bloginfo('wpurl'); ?>/cmap/functions?action=delete&id=<?php echo $id; ?>&token=<?php echo $token; ?>'"/>
<?php
                } else {
?>
        <input type="submit" value="Eintragen"/>
<?php
                }
            }
?>
        <input type="button" value="Zurück" onclick="window.history.back();"/>
        
    </form>
  
    <script src="../cmap-resources/jquery/jquery-3.3.1.min.js"></script>
    
    <script type="text/javascript">
        window.onmessage = function(e){
            $("#lat").val(e.data.lat);
            $("#lon").val(e.data.lng);
        };
        
        function form_validation() {            
        }
    </script>
<?php
        } else {
?>
    Falsche Zugangsdaten oder Status
<?php
        }
    }

?>

</div>
<?php
    get_footer();
?>
