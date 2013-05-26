<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


class CouchDBException extends Exception{
    
       public function __construct($message, $code = 0, Exception $previous = null) {

        parent::__construct($message, $code, $previous);
    }


    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }


}

?>
