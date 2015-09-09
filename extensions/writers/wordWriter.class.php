<?php

class WordWriter
{    
    public static function write(DisaggregatorPerson $person, Document $document, $redactions)
    {                 
        $root = $_SERVER['DOCUMENT_ROOT'];
        $documentDir = "sites/Disaggregator2/data/documents/";        
        $ext = Document::getExtension(basename($document->Filepath));
        
        //derive new location
        $aname = 'd_' . $person->UserID . '_' . uniqid();
        $new = iotaPath::makepath($documentDir, $aname . '.' . $ext);  
        
        //get reference to old location
        $old = $documentDir . $document->Filepath;
        
        copy($old, $new);
        
        //get document.xml
        $xml = WordReader::getDocumentXML($document);
        //modify document.xml appropriately for each redaction
        foreach($redactions as $redaction)
        {
            switch($redaction->style)
            {
                case "para":
                    $xml = self::textRedaction($xml, $redaction);
                    break;
                case "heading":
                    $xml = self::textRedaction($xml, $redaction);
                    break;
                case "caption":
                    $xml = self::textRedaction($xml, $redaction);
                    break;
            }
        }
        //write the new xml to a temporary file
        $temp = iotaPath::makepath($documentDir, $aname . "_document.xml");
        $xml->asXML($temp);
        
        //insert new document.xml into zip archive
        $zip = new ZipArchive();        
        if($zip->open($new) === TRUE)
        {
            $zip->addFile($temp, 'word/document.xml');
            $zip->close();        
        }      
        
        //delete temp file
        unlink($temp);
        
        //return the path of the new document
        return $aname . '.' . $ext;
    }   
    
    public static function textRedaction($xml, $redaction)
    {                
        //change the text of the first text element to the new content
        $xml->xpath("$redaction->xpath/w:r[1]/w:t")[0]->{0} = $redaction->content;
        
        //remove content from subsequet text elements for this paragraph
        $i = 2;
        foreach($xml->xpath("$redaction->xpath/w:r[position()>1]") as $element)
        {   
            //if there is a text element
            if(count($xml->xpath("$redaction->xpath/w:r[$i]/w:t")) > 0)
            {
                 $xml->xpath("$redaction->xpath/w:r[$i]/w:t")[0]->{0} = "";
            }
            $i++;
        }
        return $xml;
    }
}

?>


