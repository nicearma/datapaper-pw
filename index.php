<?php

/*
  Plugin Name: Datapaper
  Plugin URI: http://dataconf.liris.cnrs.fr
  Description: This is a plugin for the dataconf.
  Version: 0.5
  Author: ARMANDO Nicolas
  Author URI: http://www.nicearma.com
  License: A "Slug" license name e.g. GPL2
 */

/*
 *All the script necessary for the plugin
 */

wp_register_script('dp-plugin-script', plugins_url('/js/plugin.js', __FILE__), array('jquery', 'jquery-ui-dialog'));
wp_register_script('dp-sha1', plugins_url('/js/sha1.js', __FILE__));
wp_register_script('dp-admin-script', plugins_url('/js/admin.js', __FILE__), array('jquery', 'jquery-ui-dialog'));
wp_register_script('dp-creator-script', plugins_url('/js/creator.js', __FILE__), array('jquery', 'jquery-ui-dialog', 'jquery-ui-accordion'));
wp_register_script('dp-help-script', plugins_url('/js/help.js', __FILE__), array('jquery', 'jquery-ui-tabs', 'jquery-ui-autocomplete','jquery-ui-dialog'));
wp_register_script('dp-bootstratp', plugins_url('/js/bootstrap.min.js', __FILE__), array('jquery'));
wp_register_script('dp-form-registration', plugins_url('/js/form.js', __FILE__), array('jquery'));
wp_register_style('dp-bootstratp-css', plugins_url('css/bootstrap.min.css', __FILE__));


add_action('admin_init', 'dp_help_files');
add_action('admin_menu', 'dp_menu');

add_filter('login_redirect', 'dp_init', 10, 3);

/**
 * Add the menu in the left bar of the wordpress admin bar
 * All the 3 param automatic came from the wordpress hook
 * @param type $redirect_to
 * @param type $request
 * @param type $user
 * @return string
 */

function dp_init($redirect_to,$request,$user){
    if(isset($user->roles)&&  is_array($user->roles)){
        
        if(in_array("administrator", $user->roles)){
            $redirect_to=  get_settings('siteurl').'/wp-admin/admin.php?page=dp-admin';
        }else if(in_array("editor", $user->roles)){
            $redirect_to=  get_settings('siteurl').'/wp-admin/admin.php?page=dp-chair';
        }else if(in_array("subscriber", $user->roles)){
            $redirect_to=  get_settings('siteurl').'/wp-admin/admin.php?page=dp-user';
        }
    }else{
        
    }
    return $redirect_to;
}

function dp_menu(){
    add_menu_page('Datapaper plugin', 'Datapaper', 'activate_plugins', 'dp-menu-page','dp_home');
    add_submenu_page('dp-menu-page', 'Datapaper admin','Admin', 'activate_plugins','dp-admin','datapaper_admin');
    add_submenu_page('dp-menu-page', 'Datapaper chair','Chair', 'edit_posts', 'dp-chair','datapaper_creator');
    add_submenu_page('dp-menu-page', 'Datapaper user','User', 'read', 'dp-user','datapaper_user');
}
/*
 * Add files php who can work in different part of the code
 */

function dp_help_files() {
    
    wp_enqueue_script('dp-help-script');
    wp_enqueue_script('dp-sha1');
    wp_enqueue_script('dp-bootstratp');
    wp_enqueue_style('dp-bootstratp-css');

}

/*
 * Zone fot add all the file for work with the plugin
 */

include_once 'modele/DPData.php';
include_once 'controler/Info.php';
include_once 'controler/Auto.php';
include_once 'modele/Validator.php';
include_once 'connection/CouchDB.php';
include_once 'connection/CouchDBException.php';
include_once 'connection/CouchDBRequest.php';
include_once 'connection/CouchDBResponse.php';

include_once 'modele/DPData.php';
include_once 'modele/Install.php';
include_once 'modele/DP_SQL.php';
include_once 'modele/Configuration.php';
include_once 'modele/Sparql.php';
include_once 'html/help.php';
include_once 'html/admin.php';
include_once 'html/creator.php';
include_once 'html/user.php';
include_once 'html/home.php';
include_once 'html/form_registration.php';

//for add in the database the news tables *see note int the file DP_SQL
register_activation_hook(__FILE__, 'DP_SQL_install');

//Use in different part of the code for get the code SPARQL 
function get_sparql($id,$sparql){
        //get from the database the end point and the uri for make the different communication between 
        $result= DP_SQL::DP_get_source($id);
        //replace the uri generique from the $spaql for the uri base
        $sparql = str_replace(Configuration::$url, '<'.$result[0]->uri_base.'>', $sparql);
        
        $uri_sparql = $result[0]->uri_sparql;
        //get the result from the end point and the query sparql
        return Sparql::sparqlQuery($sparql, $uri_sparql);
 
}
?>
