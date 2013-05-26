<?php

add_action( 'admin_init', 'datapaper_plugin_init' );

function datapaper_plugin_init(){
    add_action('wp_ajax_send_info', 'send_info_callback');
    add_action('wp_ajax_delete_info', 'delete_info_callback');
   wp_enqueue_script('dp-plugin-script');
}


function datapaper_user() {
    echo '<script type="text/javascript">var urlPath="../'.plugin_dir_url(__FILE__).'";</script>'
    ?>

    <div id="datapaper">
        <h1>DATAPAPER USER</h1>
        <p>This plugin is for send information to the data base CouchDB in dataconf</p>
        <p>For this moment you only can search the url in the local data base</p>
        <p>you can add differente value for type, but if you can have somme special type like</p>
        <ol><li>user-photo</li><li>user-twitter</li><li>user-web</li><li>user-phone</li></ol>
        <hr>
        <div id="datapaper_search">
            <?php
            global $current_user;
            get_currentuserinfo();
            $result = DP_SQL::DP_get_URI($current_user->ID);
            if(count($result)>0){
            echo '<select id="key">';
            foreach ($result as $key => $value) {
                echo '<option value="' . $value->uri . '">' . $value->uri . '</option>"';
            };
            echo '</select>';
          
                echo   '<button onclick="DP_searchValeu()" class="button button-primary">Search</button>';
            }else{
               echo '<h3>You dont have any uri for put information</h3>'; 
            }
            
            ?>
            
          
            
            <hr>
        </div>
        <div id="datapaper_info">

        </div>

        <div id="datapaper_command" class="buttoncontainer" hidden="">
            <button type="button" id="datapaper_send" class="button button-primary" onclick="DP_sendValue()">Send</button>
            <button type="button" id="datapaper_delete" onclick="DP_DeleteValue()" class="button button-secondary">Delete</button>
            <button type="button" id="datapaper_cancel" class="button button-secondary" onclick="DP_cancel()">Cancel</button>
        </div>    



    </div>

    <?php
}


function send_info_callback() {
    $info = new Info();
    $info->makeConnection();
    $info->setInfo($_POST['value']);
    $info->addPrivate();
    $info->sendInfo();
    die();
}

function delete_info_callback() {
    $info = new Info();
    $info->makeConnection();
    $info->setInfo($_POST['value']);
    $info->addPrivate();
    //  $info->deleteInfo();
    die();
  
}
?>
