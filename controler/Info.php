<?php

/*
 * Relation between the Object of connection and the plugin PHP (coonection/CouchDB*)
 */

class Info {

    private $info;
    private $dataBase;

    public function setInfo($info) {
        $this->info = $info;
    }

    /**
     * Add the information necessary for the database couchDB, this information is for know who add this in the database CouchDB
     * @global type $current_user
     * @param type $userLogin
     */
    public function addPrivate($userLogin = null) {
        if (empty($userLogin)) {
            global $current_user;
            get_currentuserinfo();
            $userLogin = $current_user->user_login;
        }
        $out = ["private" => [ "edit_by" => ["url" => $_SERVER['HTTP_HOST'], "name" => $userLogin]]];
        $this->info = array_merge($this->info, $out);
    }

    public function sendInfo() {
        return $this->actionInfo('post');
    }

    public function deleteInfo() {
        $this->actionInfo('delete');
    }

    public function actionInfo($type) {
        if (!empty($this->info)) {
            $this->info = json_encode($this->info);
            $result = $this->dataBase->send('/', $type, $this->info);
            return $result->getBody(true);
        }
    }

    public function makeConnection() {
        $this->dataBase = new CouchDB(Configuration::$DBName, Configuration::$DBUrl, Configuration::$DBPort, Configuration::$DBUser, Configuration::$DBCouch);
    }

}

?>
