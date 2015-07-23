<?php

/* 
 * Reads a given word file, extracting the text and images so that we might 
 * redact or disaggregate it.
 */

class WordReader
{
    public static function read(Document $document)
    {              
        $root = $_SERVER['DOCUMENT_ROOT'];
        $zip = zip_open($root . "sites/Disaggregator2/data/documents/$document->Filepath");        
        
        $zipEntry = zip_read($zip);
        
        while ($zipEntry != false)
        {
            //read through all the files, call appropriate functions for each            
            $entryName = zip_entry_name($zipEntry);
            //for image files
            //if (strpos($entryName, 'word/media/') !== FALSE) {
            //    $this->imageLinks[] = $this->readImage($entryName, $zipEntry);
            //}
            
            //for image rels
            //if(strpos($entryName, 'word/_rels/document.xml.rels') !== FALSE)
            //{
            //    $this->rels = $this->readRels($zipEntry);
            //}
            
            //for document content
            if (strpos($entryName, 'word/document.xml') !== FALSE)
            {
                $text = WordReader::readText($zipEntry);
            }            
            $zipEntry = zip_read($zip);
        }       
        
        return $text;
    }   
        
    public static function readText($zipEntry)
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
            
            //get the text for each
            $content = '';
            $text = $element->xpath('w:r/w:t');
            foreach($text as $t)
            {
                $content = $content . $t;
            }
            
            //get the style for each
            $styleVal = null;
            $pStyle = $element->xpath('w:pPr/w:pStyle');
            if(count($pStyle) == 1)
            {
                //process the style
                $val = $pStyle[0]->xpath('@w:val')[0];
                if(strpos($val, "Heading") === 0)
                {
                    $style = "heading";
                    $styleVal = intval(substr($val, 7));
                }
                elseif(strpos($val, "Caption") === 0)
                {
                    $style = "caption";
                }
                else //style not yet supported
                {
                    $style = "para";
                }
            }
            else
            {
                $style = "para";
            } 
            
            //construct a viewable element
            $results[] = new Viewable($content, $xpath, $style, $styleVal);
        }
        
        return $results;
    }       
}

?>


