<?php

/* 
 * Use the ChemicalTagger library to scan a document for compounds. 
 * A compound descriptor is expected to have a field called "Name".
 */

class ChemTaggerCompounds implements DocumentScanner
{    
    public static function getScannerRecord()
    {
        return Scanner::getScannerByClassName("ChemTaggerCompounds");
    }
    
    public static function read(Document $document)
    {   
        //requires plain text version of the document
        $plainText = $document->getPlainText();                   
        
        //get java path
        $javaDir = "sites/Disaggregator2/java/";
        
        //run the java        
        exec('java -cp ' . $javaDir . '/chemicaltagger:' . $javaDir . '/chemicaltagger/* ChemTagger "' . $plainText . '" "OSCAR-CM"', $output);
        
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