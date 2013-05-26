<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DPInsertSQL
 *
 * @author nicolas
 */
class DP_SQL {

    /**
     * $data is a vector with un id
     * @global type $wpdb
     * @param type $data
     */
    public static function DP_insert_uri_user($data) {
       global $wpdb;
       
       $sql='SELECT * FROM '.Install::getPrefix1(). ' where id_user='. $data['id_user'].' and uri="'. $data['uri'].'";';
       $result=$wpdb->get_results ($sql);
       if (!(count($result)>0)){
           $sql='INSERT INTO ' . Install::getPrefix1() . '(id_user, uri, name) values (' . $data['id_user'] . ',"' . $data['uri'] .'","' . $data['name'] . '");';
          // echo $sql;
           $wpdb->query($sql);
           $out=['succes'=>true,'msg'=>'The add of '.$data['uri'].' was ok!']; 
           
       }else{
           $out=['succes'=>false,'msg'=>'The realation between is already there'];;
       }
        return $out;
    }

    public static function DP_insert_source($data) {
        global $wpdb;
        $sql='INSERT INTO ' . Install::getPrefix2() .
                        '(uri_sparql, uri_base, name_uri_base) VALUES("' . $data['uri_sparql'] .
                        '","' . $data['uri_base'] . '","' . $data['name_uri_base'] . '");';
        //echo $sql;
        return $wpdb->query($sql);
    }
    
      public static function DP_get_source($id = null) {
        global $wpdb;
        if (empty($id)) {
            $sql = "select * from " . Install::getPrefix2() . ";";
        } else if (is_numeric($id)) {
            $sql = "select * from " . Install::getPrefix2() . " where id= $id;";
        } else {
            $sql = "select * from " . Install::getPrefix2() . " where name LIKE  '%$id%';";
        }
       // echo $sql;
        return $wpdb->get_results($sql);
    }

    public static function DP_get_source_auto() {
        global $wpdb;
        
            $sql = "select id,name_uri_base as name from " . Install::getPrefix2() . ";";
     
        return $wpdb->get_results($sql);
    }
    public static function DP_update_source($data) {
        global $wpdb;
        $sql='UPDATE ' . Install::getPrefix2() . ' SET  uri_sparql =\'  ' . $data['uri_sparql'] . '\', uri_base =\'  ' . $data['uri_base'] . '\', name_uri_base =\'  ' . $data['name_uri_base'] . '\' WHERE  id =' . $data['id'] . ';';
        return $wpdb->query($sql);
    }

    public static function DP_insert_relation_user_source($data) {
        global $wpdb;
        return $wpdb->query('INSERT INTO ' . Install::getPrefix3() .
                        '(id_user,id_source) VALUES(' . $data['id_user'] .
                        ',"' . $data['id_source'] . '");');
    }

    public static function DP_delete_relation_source_user($id) {
        global $wpdb;
        $result;
        $sql = 'DELETE from ' . Install::getPrefix3() .' where id=' . $id . ';';
       // echo $sql;
        return $wpdb->query($sql);
    }
  

    public static function DP_get_URI($id) {
        global $wpdb;
        return $wpdb->get_results("select id, uri from " . Install::getPrefix1() . " where id_user=" . $id . ";");
    }

    public static function DP_get_user($value=null) {
        global $wpdb;
        $sql="select id, user_login as name, sha1(user_email) as mailEncryp from " . Install::getPrefix4() ;
        if(!empty($value)){
          $sql.=  " where user_login like '%" . $value . "%'";
        }
         $sql.=" ;";
        
        return $wpdb->get_results($sql);
    }

    public static function DP_get_source_by_user($id = null) {
        global $wpdb;
        $sql = "select " . Install::getPrefix3() . ".id,"
                . Install::getPrefix2() . ".name_uri_base," . Install::getPrefix2() . ".uri_base,"
                . Install::getPrefix2() . ".uri_sparql,"
                . Install::getPrefix4() . ".user_login from " . Install::getPrefix2()
                . "," . Install::getPrefix3() . ",".Install::getPrefix4()." where ";

           $sql .=  Install::getPrefix3() . ".id_user=".Install::getPrefix4(). ".id and ". Install::getPrefix3() . ".id_source=" . Install::getPrefix2() . ".id";
       if(!empty($id)) {
           $sql.=' and '. Install::getPrefix3() .".id_user=" . $id ;
        }
        $sql.=" ;";
       // echo $sql;
        $result=$wpdb->get_results($sql);
        return $result;
    }

    public static function DP_add_user($username, $email) {
        $username = trim($username);
        $username = str_ireplace(" ", "", $username);
        $id= wp_create_user($username, wp_generate_password(), $email);
        return $id;
    }
    /*
    public static function DP_search_mail_encryp($mailEncryp){
         global $wpdb;
        $sql='SELECT id FROM '.Install::getPrefix4().' WHERE mail_encryp=\''.$mailEncryp.'\' ;';
        
         return $wpdb->get_results($sql );
    }*/
    
    public static function DP_insert_mail_encryp($mailEncryp,$id){
        global $wpdb;
        $sql='update '.Install::getPrefix4().' set mail_encryp=\''.$mailEncryp.'\' WHERE id='.$id. ' ;';
        $result = $wpdb->query($sql );
    }
    
    private static function makeValue($values){
        $out="";
        for ($i = 0; $i < count($values)-1; $i++) {
           $out.= $values[$i].' , ';
        }
    $out.=$values[count($values)-1];
    return $out;
    }

}

?>
