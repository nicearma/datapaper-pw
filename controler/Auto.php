<?php

class Auto {

    private $exist_mail = false;
    private $mail;
    private $head;
    private $results;
    private $id_source;
    private $id_user;

    function __construct($id_source, $mail) {
        $this->id_source = $id_source;
        $this->mail=$mail;
    }
    
    public function get_results() {
        return $this->results;
    }

    
    public function set_mail($mail) {
        $this->mail = $mail;
    }

    public function set_id_user($id_user) {
        $this->id_user = $id_user;
    }

    public function verify_mail() {

        $result = DP_SQL::DP_get_source($this->id_source);

        $sparql = Configuration::validate_user($this->mail);
        
        $sparql = str_replace(Configuration::$url, $result[0]->uri_base, $sparql);
        
        $uri_sparql = $result[0]->uri_sparql;

        $result = Sparql::sparqlQuery($sparql, $uri_sparql);
        $this->head = $result->head->vars;
        $this->results = $result->results->bindings;
        if (count($this->results) > 0) {
            $this->exist_mail = true;
        }
        return $this->exist_mail;
    }

    public function get_entities_author() {
       
        $sparql = Configuration::get_sparql_entities_author($this->mail);
        $result= get_sparql($this->id_source,$sparql, $result);
        

        $this->head = $result->head->vars;
        $this->results = $result->results->bindings;
       if(!(count($result->results->bindings)>0)){
           return  new WP_Error('datapaper', __("Dont find any entity author"));
       }
    }

    public function get_entities_publication() {

        $sparql = Configuration::get_sparql_entities_publication($this->mail);
        $result= get_sparql($this->id_source,$sparql, $result);


        $this->head = $result->head->vars;

        $this->results = $result->results->bindings;
        if(!(count($result->results->bindings)>0)){
           return  new WP_Error('datapaper', __("Dont find any entity publication"));
       }
    }

    public function insert_entities($id_user = null) {
        if (!empty($id_user)) {
            $this->id_user = $id_user;
        }
       
        foreach ($this->results as $value) {
            
            $data = ['name' => $value->name->value, 'uri' => $value->uri->value, 'id_user' => $this->id_user];
            DP_SQL::DP_insert_uri_user($data);
        }
    }

    /*
      public function insert_entities_publication() {
      foreach ($this->results as $key => $value) {

      foreach ($this->head as $key2 => $value2) {

      print 'value of type '. $value->$value2->type.' value of value '. $value->$value2->value.'<br>';
      }
      }
      }
     */
}

?>
