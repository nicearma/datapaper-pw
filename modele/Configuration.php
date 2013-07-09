<?php

class Configuration {
    /*
     * Valeur de configuration pour la communication couchDB
     */

    public static $DBName = 'datapaper';
    public static $DBUrl = 'localhost';
    public static $DBPort = 5984;
    public static $DBUser = 'datacouch';
    public static $DBCouch = 'Data..Couch';
    public static $url = 'uri_base';
    //All the prefix for the query 
    private static $prefix = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX swrc: <http://swrc.ontoware.org/ontology#>
PREFIX swrc-ext: <http://www.cs.vu.nl/~mcaklein/onto/swrc_ext/2005/05#>
PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX ical: <http://www.w3.org/2002/12/cal/ical#>
PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> ';

    /**
     * Query sparql for get all the author entities
     * @param type $mail
     * @return string
     */
    public static function get_sparql_entities_author($mail = null) {
        $sparql = self::$prefix . 'SELECT DISTINCT ?name ?uri  ?mailEncryp WHERE  { 
            ' . self::$url . ' swc:hasRelatedDocument ?proceedings.
            ?uriPubli swc:isPartOf ?proceedings. 
                ?uri foaf:made ?publiUri. 
                ?uri foaf:mbox_sha1sum ?mailEncryp. 
                ?uri foaf:name ?name. 
                ';
        if (!empty($mail)) {
            $sparql.='FILTER regex(?mailEncryp, "' . sha1('mailto:' . trim($mail)) . '") ';
        }
        $sparql.=' } ORDER BY ASC(?name) ';
        return $sparql;
    }

    /**
     * Query sparql for get all the publication entities
     * @param type $mail
     * @return string
     */
    public static function get_sparql_entities_publication($mail = null) {

        $sparql = self::$prefix . 'SELECT DISTINCT ?name ?uri  WHERE  { 
            ' . self::$url . ' swc:hasRelatedDocument ?proceedings.                 
            ?uri swc:isPartOf ?proceedings. 
                ?authorUri foaf:made ?uri. 
                ?uri dc:title ?name. 
                ?authorUri foaf:mbox_sha1sum ?authorMailEncryp. 
                ';
        if (!empty($mail)) {
            $sparql.='FILTER regex(?authorMailEncryp, "' . sha1('mailto:' . trim($mail)) . '") ';
        }
        $sparql.=' } ORDER BY ASC(?name) ';

        return $sparql;
    }

    /**
     * Query sparql for get all the event entities
     * @param type $mail
     * @return string
     */
    public static function get_sparql_entities_event($mail = null) {

        $sparql = self::$prefix . 'SELECT DISTINCT ?name ?uri  WHERE  { 
            ' . self::$url . ' swc:hasRelatedDocument ?proceedings.                 
             ?proceedings swc:hasPart ?publiUri.
             ?talkEvent swc:hasRelatedDocument ?publiUri. 
             ?talkEvent swc:isSubEventOf ?uri.
                 ?uri rdfs:label ?name.';

        $sparql.=' } ORDER BY ASC(?name) ';
        return $sparql;
    }

    /**
     * Query sparql for search and validate if the email exist
     * @param type $mail
     * @return string
     */
    public static function validate_user($mail) {

        $sparql = self::$prefix . 'SELECT DISTINCT ?name WHERE  { 
              ' . self::$url . ' swc:hasRelatedDocument ?proceedings.
            ?uriPubli swc:isPartOf ?proceedings. 
                ?uri foaf:made ?publiUri. 
                ?uri foaf:mbox_sha1sum ?mailEncryp. ' .
                'FILTER regex(?mailEncryp, "' . sha1('mailto:' . trim($mail)) . '") ' .
                ' } ORDER BY ASC(?name) limit 1';
        return $sparql;
    }

    /**
     * Query sparql for the conference name
     * @return type
     */
    public static function get_sparql_conference_name() {
        return self::$prefix . 'SELECT DISTINCT ?conferenceName
    WHERE { ' . self::$url . ' rdfs:label ?conferenceName.  
    }';
    }

    /**
     * Query sparql for get the conference editor
     * @param type $mail
     * @return string
     */
    public static function get_sparql_conference_editor($mail = null) {
        $sparql = self::$prefix . 'SELECT DISTINCT ?authorName ?authorUri ?authorMailEncryp WHERE {
            ' . self::$url . ' swc:hasRelatedDocument ?proceedings.
            ?proceedings swrc:editor ?authorUri. 
            ?authorUri foaf:mbox_sha1sum ?authorMailEncryp. 
            ?authorUri rdfs:label ?authorName. ';
        if (!empty($mail)) {
            $sparql.='FILTER regex(?authorMailEncryp, "' . sha1('mailto:' . trim($mail)) . '") ';
        }
        $sparql.='}';
        return $sparql;
    }

}

?>
