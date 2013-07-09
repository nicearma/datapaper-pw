<?php
/**
 * All the logic for install the plugin in the database MySQL
 * Note: not all the time this install work, if you install the plugin but in the database dont see the table *_dp_* please you have to make this tables
 */
global $datapaper_db_version;
$datapaper_db_version = "1.0";


function DP_SQL_install() {
   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   global $datapaper_db_version;
      
   $sql = "CREATE TABLE "+  Install::getPrefix1()+" (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  id_user int NOT NULL,
  uri varchar[150] NOT NULL,
  uri text NOT NULL,
  UNIQUE KEY id (id)
    );";
   dbDelta( $sql );
   $sql = "CREATE TABLE "+  Install::getPrefix2()+" (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name varchar[150] NOT NULL,
  uri_sparql varchar[255] NOT NULL,
  uri_base varchar[255] NOT NULL,
  name_uri_base text NOT NULL,
  UNIQUE KEY id (id)
    );";
   dbDelta( $sql );
   $sql = "CREATE TABLE "+  Install::getPrefix3()+" (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  id_user int NOT NULL,
  id_source int NOT NULL,
  UNIQUE KEY id (id)
    );";
   dbDelta( $sql );
   
  
   $sql = 'ALTER TABLE '.getPrefix4().' wp_users ADD mail_encryp VARCHAR( 150 ) NOT NULL AFTER user_email;'; 
   dbDelta( $sql );
   
   

   
   
   add_option( "datapaper_db_version", $datapaper_db_version );
}

class Install {
  /**
   * Table for the relationship of user-uri (entities for the user)
   * @global type $wpdb
   * @return type
   */
public static function getPrefix1(){
   global $wpdb;

   return $wpdb->prefix . "dp_user_uri";
}
 /**
  * Table for the stoke of the source end point
  * @global type $wpdb
  * @return type
  */
public static function getPrefix2(){
   global $wpdb;
   return $wpdb->prefix . "dp_source";
}
/**
 * Table for the relationship of source-user (the end point)
 * @global type $wpdb
 * @return type
 */
public static function getPrefix3(){
   global $wpdb;
   return $wpdb->prefix . "dp_source_user";
}
/**
 * Only for help and find the native table user of wordpress
 * @global type $wpdb
 * @return type
 */    

public static function getPrefix4(){
   global $wpdb;
   return $wpdb->prefix . "users";
}
/**
 * Not used, but for make all the configuration in MySQL
 * @global type $wpdb
 * @return type
 */
public static function getPrefix5(){
   global $wpdb;
   return $wpdb->prefix . "dp_configuration";
}



}

?>
