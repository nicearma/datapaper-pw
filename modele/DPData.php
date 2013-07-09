<?php

/**
 * Not used, is for validate all the data in automatic way
 *
 * @author Nicolas Armando
 */
class DPData {

    private $basic=['_id'=>["url",true],'_rev'=>["text",false]];
    
    private $typePublic = ["public"=>[
        "description" => ["text", true],
        "url" => ["link", true],
        "type" => ["text", true]]
    ];
    private $typePrivate = [
       "private"=>[ "editBy" => ["url" => ["link", true], "nom" => ["text", true]]]
    ];
    


    public function getTypePublic() {
        return $this->typePublic;
    }

    public function getTypePrivate() {
        return $this->typePrivate;
    }
    public function getBasic() {
        return $this->basic;
    }

  

    
}

?>
