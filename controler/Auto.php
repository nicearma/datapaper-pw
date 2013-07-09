<?php
/*
 * Automatic way to put the information find with the email of the user
 */

class Auto {

    private $exist_mail = false;
    private $user_email;
    private $head;
    private $results;
    private $id_source;
    private $id_user;
    /**
     * 
     * @param type $id_source
     * @param type $user_email
     */
    function __construct($id_source, $user_email) {
        $this->id_source = $id_source;
        $this->user_email = $user_email;
    }
    /**
     * get the result of the request made with the end point sparql and the sparql with the email of the user 
     * @return type
     */
    public function get_results() {
        return $this->results;
    }
    /**
     * IMPORTANT set the email of the user we want to search in the end point
     * @param type $user_email
     */
    public function set_mail($user_email) {
        $this->user_email = $user_email;
    }

    public function set_id_user($id_user) {
        $this->id_user = $id_user;
    }

 /**
  * With the email set in the object we search if exist in the end point and set/return the true or false 
  * if the email is finded in the end point
  * @return type
  */
    public function verify_mail() {

        //get the sparql with the email
        $sparql = Configuration::validate_user($this->user_email);

       //get the result for the end point and the query for validate the email
        $result = get_sparql($this->id_source, $sparql);
        //get the headers for know all the different variable
        $this->head = $result->head->vars;
        //get the list of result, is a list(array)
        $this->results = $result->results->bindings;
        //if the cantity of result is more than 0 the email exist in the end point, so the result is true
        if (count($this->results) > 0) {
            $this->exist_mail = true;
        }
        return $this->exist_mail;
    }

    public function get_entities_author() {

        //get the query sparql for try to find all the entities 
        $sparql = Configuration::get_sparql_entities_author($this->user_email);
        //get the all entities from the end point with the query for get the author
        $result = get_sparql($this->id_source, $sparql);
         //get the headers for know all the different variable
        $this->head = $result->head->vars;
        //get the list of result, is a list(array)
        $this->results = $result->results->bindings;
        //verify if the result have or not the the entitys, if dont exist any, we send un error to wordpress
        if (!(count($result->results->bindings) > 0)) {
            return new WP_Error('datapaper', __("Dont find any entity author"));
        }
    }

    public function get_entities_publication() {
        //get the query sparql for find all the entities publication 
        $sparql = Configuration::get_sparql_entities_publication($this->user_email);
        
        //get the all entities from the end point with the query for get the author
        $result = get_sparql($this->id_source, $sparql);
         //get the headers for know all the different variable
        $this->head = $result->head->vars;
        //get the list of result, is a list(array)
        $this->results = $result->results->bindings;
        //verify if the result have or not the the entitys, if dont exist any, we send un error to wordpress
        if (!(count($result->results->bindings) > 0)) {
            return new WP_Error('datapaper', __("Dont find any entity publication"));
        }
    }
    /**
     * Insert all the entities find in the function get_entities_*
     * @param type $id_user
     */

    public function insert_entities($id_user = null) {
        
        if (!empty($id_user)) {
            $this->id_user = $id_user;
        }
        //For all the result insert one by one the uri with the relation of the user
        foreach ($this->results as $value) {

            $data = ['name' => $value->name->value, 'uri' => $value->uri->value, 'id_user' => $this->id_user];
            DP_SQL::DP_insert_uri_user($data);
        }
    }

    /**
     * Insert in CouchDB if the image exist
     * @param type $id_user
     */
    public function insert_gravatar($email,$userLogin=null) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $grav_url = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($email)));
            $file_headers = @get_headers($grav_url);
            if (!($file_headers[0] == 'HTTP/1.1 404 Not Found')) {
                $data = ['public' => ['0' => ['description'=>'', 'url' => $grav_url,'type'=>'']]];
                $info=new Info();
                foreach ($this->results as $value) {
                     $info->setInfo(array_merge(['_id'=>$value],$data));
                     $info->addPrivate($userLogin);
                }
            }
        }
    }

}

?>
