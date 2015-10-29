<?php

/* 
 * Reads a given powerpoint file, extracting the text and images so that we might 
 * redact or disaggregate it.
 */


class PowerpointReader extends OpenXMLReader
{    
    public static function read(Document $document)
    {                      
        $root = $_SERVER['DOCUMENT_ROOT'];
        $documentDir = "sites/Disaggregator2/data/documents/";
        
        //derive image directory path
        $imageDir = $documentDir . substr($document->Filepath, 0, strpos($document->Filepath, "."));              
        
        //now get everything else (i.e. images and text)
        $text = array();
        $zip = zip_open($root . $documentDir . $document->Filepath);             
        $zipEntry = zip_read($zip);   
        while ($zipEntry != false)
        {                        
            //read through all the files, call appropriate functions for each            
            $entryName = zip_entry_name($zipEntry);
            //for image files
            if (strpos($entryName, 'ppt/media/') !== FALSE) {
                self::readImage($imageDir, $entryName, $zipEntry);
            }
            
            //for document content
            if (strpos($entryName, 'ppt/slides/slide') !== FALSE)
            {
                //get slide number                
                $start = 16;
                $length = strpos($entryName, '.xml') - $start;
                $no = substr($entryName, $start, $length);
                
                //read slide content
                $text[$no] = self::readText($zipEntry, $imageDir, $relList, $no);
            }            
            $zipEntry = zip_read($zip);
        }      
        
        //sort the array
        ksort($text);
        
        //merge results in to one single array
        $slides = array();
        foreach($text as $slide)
        {
            $slides = array_merge($slides, $slide);
        }
        
        return $slides;
    }           
    
    public static function readText($zipEntry, $imageDir, $relList, $no)
    {
        $results = array();       
        $results[] = new Viewable("Slide " . $no++, "slide");
        
        $doc = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
        $xml = simplexml_load_string($doc);
   
        //read all the text elements
        $elements = $xml->xpath('/p:sld/p:cSld/p:spTree/p:sp');       
        foreach($elements as $element)
        {
            //get the xpath for each
            $node = dom_import_simplexml($element);
            $xpath = $node->getNodePath();           
                                    
            //get the text for each
            $content = '';
            $paraElements = $element->xpath('p:txBody/a:p/a:r/a:t'); //we only want text 
            foreach($paraElements as $pe)
            {
                //get the path for the pe
                $peNode = dom_import_simplexml($pe);         
                $content = $content . $pe;                
            }       
                                    
            //construct a viewable element
            $results[] = new Viewable($content, "para", null, $xpath);
        }        
        return $results;
    }           
}

?>


