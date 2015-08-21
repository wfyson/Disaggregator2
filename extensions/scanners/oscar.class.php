<?php

/* 
 * Use the OSCAR Java library to scan a document for compounds. 
 * A compound descriptor is expected to have a field called "Name".
 */

class OscarCompounds implements DocumentScanner
{    
    public static function getScannerRecord()
    {
        return Scanner::getScannerByClassName("OscarCompounds");
    }
    
    public static function read(Document $document)
    {   
        //requires plain text version of the document
        $plainText = $document->getPlainText();                   
        
        //get java path
        $javaDir = "sites/Disaggregator2/java/";
        
        //run the java
        exec('java -cp ' . $javaDir . '/oscar:' . $javaDir . '/oscar/* OscarFile "' . $plainText . '"', $output);
        
        //prepare the results
        $results = array();
        
        //get the descriptor
        $scanner = self::getScannerRecord();
        $descriptor = $scanner->getDescriptor();
        
        //get the fields about components we want to fill in, in this instance the name field
        $descriptorFields = $descriptor->getdescriptorfields();
        $i = $descriptorFields->getIterator();
        while($i->hasNext())
        {
            $descriptorField = $i->next();
            $field = $descriptorField->getDisaggregatorField();
            if($field->Name == "Name")
            {
                $fieldID = $field->FieldID;
            }
        }
        
        //generate an associative array for each result
        $names = array();
        foreach($output as $name)
        {   
            if(!in_array(strtolower($name), $names))
            {
                $names[] = strtolower($name);
                $result = array($fieldID => $name);            
                $results[] = $result;
            }            
        }
        
        return $results;
    }   
}

?>