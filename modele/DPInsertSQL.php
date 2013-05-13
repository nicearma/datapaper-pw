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
    public static function DP_data($data) {
        global $wpdb;
        return $wpdb->query('INSERT INTO ' . Install::getPrefix() . '(id_user, uri) VALUES(' . $data['id_user'] . ',"' . $data['uri'] . '");');
    }

    public static function DP_get_URI($id) {
        global $wpdb;
 
        return $wpdb->get_results("select uri from " . Install::getPrefix() . " where id_user=" . $id.";");
    }

    public static function DP_add_user($username, $email = null) {
        $username = trim($username);
        $username = str_ireplace(" ", "", $username);
        return wp_create_user($username, $username, $email);
    }

}

?>
