<?php

global $datapaper_db_version;
$datapaper_db_version = "1.0";


function DP_SQL_install() {
   global $datapaper_db_version;
      
   $sql = "CREATE TABLE "+  Install::getPrefix()+" (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  id_user int NOT NULL,
  uri varchar[150] NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "datapaper_db_version", $datapaper_db_version );
}

class Install {
  
public static function getPrefix(){
   global $wpdb;
  // global $datapaper_db_version;

   return $wpdb->prefix . "dp_user_uri";
}
        

}

?>
