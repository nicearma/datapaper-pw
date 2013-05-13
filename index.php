<?php
/*
  Plugin Name: Datapaper
  Plugin URI: http://dataconf.liris.cnrs.fr
  Description: This is a plugin for the dataconf.
  Version: 0.1
  Author: ARMANDO Nicolas
  Author URI: http://www.nicearma.com
  License: A "Slug" license name e.g. GPL2
 */

include_once 'modele/DPData.php';
include_once 'controler/Info.php';
include_once 'modele/Validator.php';
include_once 'connection/CouchDB.php';
include_once 'connection/CouchDBRequest.php';
include_once 'connection/CouchDBResponse.php';
include_once 'modele/DPData.php';
include_once 'modele/Install.php';
include_once 'modele/DPInsertSQL.php';

add_action('admin_menu', 'datapaper_plugin_menu');
add_action( 'admin_init', 'datapaper_plugin_init' );
 wp_register_script( 'dp-help-script', plugins_url( '/js/help.js', __FILE__ ), array('jquery','jquery-ui-tabs') );
 wp_register_script( 'dp-plugin-script', plugins_url( '/js/plugin.js', __FILE__ ),array('jquery') );
 wp_register_script( 'dp-admin-script', plugins_url( '/js/admin.js', __FILE__ ),array('jquery') );
 
function datapaper_plugin_init(){
    add_action('wp_ajax_send_info', 'send_info_callback');
    add_action('wp_ajax_delete_info', 'delete_info_callback');
   
}


register_activation_hook(__FILE__, 'DP_SQL_install');

if (is_admin()) {
    add_action( 'admin_init', 'datapaper_admin_init' );
    add_action('admin_menu', 'datapaper_admin_menu');
    
    add_action('wp_ajax_add_user', 'add_user_callback');
    add_action('wp_ajax_search_user', 'search_user_callback');
    add_action('wp_ajax_add_uri', 'add_uri_callback');
    
}
function datapaper_admin_init(){
   
    
}
function datapaper_admin_menu() {
    add_options_page('DATAPAPER ADMIN OPTIONS', 'Datapaper Admin', 'manage_options', 'datapaper-admin', 'datapaper_admin');
    wp_enqueue_script('dp-help-script');
    wp_enqueue_script('dp-admin-script');
}

function datapaper_plugin_menu() {
  add_options_page('Datapaper options', 'Datapaper User', 'read', 'datapaper-plugin', 'datapaper_html');
  wp_enqueue_script('dp-help-script');
  wp_enqueue_script('dp-plugin-script');
    
}

function datapaper_html() {
    echo '<script type="text/javascript">var urlPath="'.plugin_dir_url(__FILE__).'";</script>'
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
            // var_dump($result);
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

function datapaper_admin() {
    ?>
    <h1>DATAPAPER ADMIN</h1>
   
    
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">Search by Sparql</a></li>
    <li><a href="#tabs-2">Add uri to user</a></li>
  </ul>
  <div id="tabs-1">
      <h3>Search by Sparql</h3>
    <p>Here you can put different user to the data base</p>
    <p>For this moment this plugin only work with the xml from datasemanticweb</p>
    <p>Please enter the Request to the server</p>
    <p><b>Ex for the url</b></p>
    <pre>
    http://posters.www2012.org:8080/sparql/?
    </pre>
<p><b>Ex for the Sparql</b></p> <pre>
PREFIX swc: &#60;http://data.semanticweb.org/ns/swc/ontology#>
PREFIX foaf: &#60;http://xmlns.com/foaf/0.1/>  
SELECT DISTINCT ?authorName  ?authorUri ?uriPubli WHERE  {     
?uriPubli swc:isPartOf  &#60;http://data.semanticweb.org/conference/www/2012/proceedings>.
?authorUri foaf:made ?uriPubli.                       
?authorUri foaf:name ?authorName.         
} ORDER BY ASC(?authorName) 
    </pre>
    <h3>URL</h3>
    <input type="url" name="url" id="url" />
    <h3>Sparql</h3>
    <textarea rows="10" cols="100"></textarea><br/>
    <input type="button" onclick="DP_search_sparql();" value="Make">
    <form id="dp_user">

    </form> </div>
  <div id="tabs-2">
      <h3>Here you can add some uri to the user</h3>
      <form id="dp-uri">
      <div id="dp-search-user">
      <p><b>Search the user</b></p>
      <input type="text" name="user" id="dp-user">
      <input type="button" value="search user" onclick="DP_search_user()">
      </div>
      <div hidden id="dp-add-uri">
      
          <input type="button" value="send" onclick="DP_uri_user()"/>
      </div>
      </form>
    </div>
</div>

    <?php
    //wp_generate_password()
}

function send_info_callback() {
    $info = new Info();
    $info->makeConnection();
    $info->setInfo($_POST['value']);
    $info->addPrivate();
    $info->sendInfo();
}

function delete_info_callback() {
    $info = new Info();
    $info->makeConnection();
    $info->setInfo($_POST['value']);
    $info->addPrivate();
  //  $info->deleteInfo();
}


function add_user_callback() {
    $value = $_POST['value'];
    
    $id = DP_SQL::DP_add_user($value['user']);
    if (is_integer($id)) {
       add_uri_callback($value,$id);
    }
}
    
function search_user_callback(){
    $value=$_POST['value'];
    $blogusers = get_users('search='.$value.'*');
    
    foreach ($blogusers as $user) {
        echo '<br/><input type="checkbox" name="id" value="'.$user->id.'">'. $user->user_login;
    }
}
    
function add_uri_callback($value=null,$id=null){
    
    var_dump($_POST);
    if($value==null){
        $value=$_POST['value'];
        $id= $value['id'];
    }
     foreach ($value['uri'] as $key => $uri) {
            $array = ['id_user' => $id, 'uri' => $uri];
          echo  DP_SQL::DP_data($array);
        }
}

?>
