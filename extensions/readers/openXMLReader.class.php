<?php

/* 
 * Reads a given word file, extracting the text and images so that we might 
 * redact or disaggregate it.
 */

define("IMAGE_REL_TYPE", "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image");

abstract class OpenXMLReader
{            
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
}

?>

