<?php

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
  
public static function getPrefix1(){
   global $wpdb;
  // global $datapaper_db_version;

   return $wpdb->prefix . "dp_user_uri";
}
 
public static function getPrefix2(){
   global $wpdb;
  // global $datapaper_db_version;

   return $wpdb->prefix . "dp_source";
}

public static function getPrefix3(){
   global $wpdb;
  // global $datapaper_db_version;

   return $wpdb->prefix . "dp_source_user";
}
    

public static function getPrefix4(){
   global $wpdb;
  // global $datapaper_db_version;

   return $wpdb->prefix . "users";
}

public static function getPrefix5(){
   global $wpdb;
  // global $datapaper_db_version;

   return $wpdb->prefix . "dp_configuration";
}



}

?>
