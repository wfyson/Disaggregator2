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
        $graph->load($uri);
              
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
    }     
}


