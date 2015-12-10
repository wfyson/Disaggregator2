<?php

include_once("arc/ARC2.php");
include_once("Graphite.php");

class LinkedDataHelper
{
    public static function validateNS($uri)
    {
        $graph = new Graphite();
        $triples = $graph->load($uri);
        error_log($triples);
        if($triples > 0)
        {
            //whilst we're here return a title if possible
            $title = $graph->resource($uri)->get("dc:title")->toString();
            error_log($title);
            if($title)
                return $title;
            else
                return true;
        }
        else
        {
            return false;
        }
    }
    
    public static function dumpNS($uri)
    {
        $graph = new Graphite();
        $graph->load($uri);
        print $graph->dump();
    }
    
    public static function getNamespaceTypes($uri, $types)
    {
        $graph = new Graphite();
        //$uri = "http://xmlns.com/foaf/0.1/";
        //$uri = "http://id.southampton.ac.uk/";
        //$uri = "http://purl.org/dc/elements/1.1/";
        //$uri = "http://www.rsc.org/ontologies/RXNO_OWL.owl#";
        $graph->load($uri);
        //print $graph->dump();
            
        //$resources = $graph->allOfType("rdf:Property");     
        $results = [];
        foreach($types as $type)
        {
            $resources = $graph->allOfType($type);
            foreach($resources as $resource)
            {
                $results[$resource->toString()] = $resource->getString( "rdfs:label" );
            }
        }                
        return $results;
        
        //print $resources->dump();
            
        ////print $graph->dump();
        //print $graph->resource( "http://xmlns.com/foaf/0.1/" )->get( "rdfs:isDefinedBy" );
            
        //error_log("results...." . count($resource));
           
        //print($resource->dumpValue());
                       
        //print $graph->dump();
        //$label_relations_list = $graph->labelRelations();
        //print_r($label_relations_list);
        //print $graph->resource( $uri )->all( "rdfs:isDefinedBy" )->sort( "rdfs:label" )->getString( "rdfs:label" )->join( ", " ).".\n";
    }     
}


