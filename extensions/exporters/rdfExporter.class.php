<?php

class RDFExporter
{
    public static function exportRDF(Component $component)
    {
        //Perhaps this can only be allowed if all the fields have URIs - otherwise how do we depict them?
        
        
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


