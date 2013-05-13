<?php


class Validator {
    
    public static function text($valeu){
       $value=filter_var($value, FILTER_SANITIZE_STRING);  
       return $value;
    }
    
    public static function link($value){
        
    if(filter_var($value, FILTER_VALIDATE_URL)){
        $value=filter_var($value, FILTER_SANITIZE_URL);
    }
    return $value;
}
    
}

?>
