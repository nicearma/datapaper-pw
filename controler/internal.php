<?php

add_action( 'lostpassword_post', 'verify_mail_encryp' );

function verify_mail_encryp(){
    $mail=esc_sql($_POST['user_login']);
    $mail=sha1($mail);
    $user=DP_SQL::DP_search_mail_encryp($mailEncryp);
    if(count($user)>0){
        wp_update_user( array ( 'ID' => $user[0]->id, 'user_mail' => $website ) ) ;
    }
}
?>
