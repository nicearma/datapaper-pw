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
/*
function dp_menu_bar(){
    global $wp_admin_bar;

    $wp_admin_bar->add_menu( array(
        'id'   => $id,
        'meta' => array(),
        'title' => $name,
        'href' => $href ) );
}*/

function dp_menu(){
    add_menu_page('Datapaper plugin', 'Datapaper', 'activate_plugins', 'dp-menu-page','dp_home');
    add_submenu_page('dp-menu-page', 'Datapaper admin','Admin', 'activate_plugins','dp-admin','datapaper_admin');
    add_submenu_page('dp-menu-page', 'Datapaper chair','Chair', 'edit_posts', 'dp-chair','datapaper_creator');
    add_submenu_page('dp-menu-page', 'Datapaper user','User', 'read', 'dp-user','datapaper_user');
}

function dp_help_files() {
    
    wp_enqueue_script('dp-help-script');
    wp_enqueue_script('dp-sha1');
    wp_enqueue_script('dp-bootstratp');
    wp_enqueue_style('dp-bootstratp-css');

}

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
register_activation_hook(__FILE__, 'DP_SQL_install');

function get_sparql($id_user,$sparql){
        $result= DP_SQL::DP_get_source($id);
        $sparql = str_replace(Configuration::$url, $result[0]->uri_base, $sparql);
        $uri_sparql = $result[0]->uri_sparql;
        return Sparql::sparqlQuery($sparql, $uri_sparql);
 
}
?>
