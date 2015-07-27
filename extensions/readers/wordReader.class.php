<?php

/* 
 * Reads a given word file, extracting the text and images so that we might 
 * redact or disaggregate it.
 */

define("IMAGE_REL_TYPE", "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image");

class WordReader
{    
    public static function read(Document $document)
    {              
        $root = $_SERVER['DOCUMENT_ROOT'];
        $documentDir = "sites/Disaggregator2/data/documents/";
        
        //derive image directory path
        $imageDir = $documentDir . substr($document->Filepath, 0, strpos($document->Filepath, "."));
        
        //first get the image rels
        $zip = zip_open($root . $documentDir . $document->Filepath);               
        $zipEntry = zip_read($zip);        
        while ($zipEntry != false)
        {
            $entryName = zip_entry_name($zipEntry);
            if(strpos($entryName, 'word/_rels/document.xml.rels') !== FALSE)
            {
                $relList = self::readRels($zipEntry);
            }
            $zipEntry = zip_read($zip);            
        }        
        
        //now get everything else (i.e. images and text)
        $zip = zip_open($root . $documentDir . $document->Filepath);               
        $zipEntry = zip_read($zip);        
        while ($zipEntry != false)
        {
            //read through all the files, call appropriate functions for each            
            $entryName = zip_entry_name($zipEntry);
            //for image files
            if (strpos($entryName, 'word/media/') !== FALSE) {
                self::readImage($imageDir, $entryName, $zipEntry);
            }
            
            //for document content
            if (strpos($entryName, 'word/document.xml') !== FALSE)
            {
                $text = self::readText($zipEntry, $imageDir, $relList);
            }            
            $zipEntry = zip_read($zip);
        }      
        
        return $text;
    }   
        
    public static function readText($zipEntry, $imageDir, $relList)
    {
        $results = array();
        
        $doc = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
        $xml = simplexml_load_string($doc);
   
        //read all the text elements
        $elements = $xml->xpath('/w:document/w:body/w:p | /w:document/w:body/w:tbl');       
        foreach($elements as $element)
        {
            //get the xpath for each
            $node = dom_import_simplexml($element);
            $xpath = $node->getNodePath();           
            
            //get the style for each
            $styleVal = null;
            $pStyle = $element->xpath('w:pPr/w:pStyle');
            if (count($pStyle) == 1)
            {
                //process the style
                $val = $pStyle[0]->xpath('@w:val')[0];
                if (strpos($val, "Heading") === 0)
                {
                    $style = "heading";
                    $styleVal = intval(substr($val, 7));
                }
                elseif (strpos($val, "Caption") === 0)
                {
                    $style = "caption";
                }
                else
                {   
                    //style not yet supported
                    $style = "para";
                }
            }
            else
            {
                $style = "para";
            }
            
            //get the text for each
            $content = '';
            $element->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
            $element->registerXPathNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
            $paraElements = $element->xpath('w:r/w:t | w:r/w:drawing/wp:*/a:graphic/a:graphicData/pic:pic/pic:blipFill/a:blip/@r:embed'); //we only want text or images (no smartart I'm afraid!)
            foreach($paraElements as $pe)
            {
                //get the path for the pe
                $peNode = dom_import_simplexml($pe);
                $pePath = $peNode->getNodePath();                
                if(strpos($pePath, "w:t") !== false) //we have a text
                {                  
                    $content = $content . $pe;
                }
                else
                {                    
                    $content = $imageDir . "/" . $relList[(string)$pe];
                    $style = "image";
                }
            }       
                                    
            //construct a viewable element
            $results[] = new Viewable($content, $xpath, $style, $styleVal);
        }        
        return $results;
    }   
    
    public static function readImage($imageDir, $entryName, $zipEntry)
    {  
        //make an image directory 
        $imageDir = $_SERVER['DOCUMENT_ROOT'] . $imageDir;
        if (!file_exists($imageDir))
        {
            mkdir($imageDir, 0777, true);
	}
        
        $img = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
        if ($img !== null)
        {
            $imagePath = $imageDir . "/" . basename($entryName);
            file_put_contents($imagePath, $img);
        }      
    }
    
    public static function readRels($zipEntry)
    {        
        $relList = array();                       
        $rels = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
        $xml = simplexml_load_string($rels);
        
        for ($i = 0; $i < $xml->count(); $i++) {
            $record = $xml->Relationship{$i};
            $type = $record->attributes()->Type;
            $cmp = strcmp($type, constant("IMAGE_REL_TYPE"));
            if ($cmp == 0)
            {
                $id = $record->attributes()->Id;
                $target = basename($record->attributes()->Target);                   
                $relList[(string)$id] = $target;           
            }
        }
        return $relList;        
    }
}

?>


