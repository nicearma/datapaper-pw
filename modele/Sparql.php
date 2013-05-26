<?php

/*

$data=sparqlQuery($query, "http://localhost:8890/sparql/");

print "Retrieved data:\n" . json_encode($data);
*/

class Sparql {

   public static function  sparqlQuery($query, $baseURL, $format = "json") {
        $params = array(
            "query" => $query,
            "output" => $format,
        );

        $querypart = "?";
        foreach ($params as $name => $value) {
            $querypart = $querypart . $name . '=' . urlencode($value) . "&";
        }

        $sparqlURL = $baseURL . $querypart;
      //  print $sparqlURL;

        return json_decode(file_get_contents($sparqlURL));
    }

}

?>
