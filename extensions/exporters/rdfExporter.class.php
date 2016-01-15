<?php

require 'sites/Disaggregator2/vendor/autoload.php';

class RDFExporter
{
    public static function exportRDF(Component $component)
    {
        $model = DisaggregatorModel::get();
                    
        $graph = new EasyRdf_Graph("http://www.disaggregator.com/component#335");
        
        //add the type if we have one
        $descriptor = $component->getDescriptor();
        if($descriptor->NamespaceID)
        {
            error_log("A type has been defined!!!!");
            $namespace = $model->namespace->getRecordByPK($descriptor->NamespaceID);    
            
            $class = $graph->resource("http://www.disaggregator.com/component#335", $descriptor->Class);
        }
        
        //foreach field addLiteral??
        
        $xml = $graph->serialise('rdfxml');
        return $xml;
        
        //Add namespaces
        
        /*
          <?xml version=“1.0”?>
          <rdf:RDF xmlns:rdf=“http://www.w3.org/1999/02/22-rdf-syntax-ns#” **Need this one**
          xmlns:dc=“http://purl.org/dc/elements/1.1/” **Add these ones on the fly based on what the component needs**
          xmlns:ex=“http://example.org/ontology#”>
          <rdf:Description> **Start with this**
          <rdf:type rdf:resource=“http://example.org/ontology#Website”/> **Add this if the descriptor has a class**
          <dc:title>Foreword</dc:title> **Descriptor Fields**         
          </rdf:Description>
          </rdf:Description>
          </rdf:RDF>
         */
    }     
}


