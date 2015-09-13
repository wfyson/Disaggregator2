<?php

/* 
 * Reads a given pdf file, extracting the text so that we might 
 * disaggregate content from it.
 */
/*
require $_SERVER['DOCUMENT_ROOT'] . 'sites/Disaggregator2/vendor/autoload.php';

class PDFReader
{    
    public static function read(Document $document)
    {              
        $root = $_SERVER['DOCUMENT_ROOT'];
        $documentDir = "sites/Disaggregator2/data/documents/";                       
        
        $parser = new Smalot\PdfParser\Parser();
        
        $pdf = $parser->parseFile($root . $documentDir . $document->Filepath); 
        
        $text = self::readText($pdf); 
        
        return $text;
    }   
        
    //produce an array of viewables
    public static function readText($pdf)
    {        
        $results = array();
                    
        $pages = $pdf->getPages(); 
        
        foreach($pages as $page){      
            //get the text from the page
            $text = $page->getText();    
            
            error_log("reading page");
            
            //error_log($text);
            $text = str_replace("\n", "<br/>", $text);
            //$paragraphs = explode("\n", $text);            
  
            $results[] = new Viewable($text, "page");                        
        }
        
        return $results;
    }   
    

}
*/

class PDFReader
{    
    public static function read(Document $document)
    {         
        //get java path
        $javaDir = "sites/Disaggregator2/java";
        
        //get the path to the PDF
        $root = $_SERVER['DOCUMENT_ROOT'];
        $documentDir = "sites/Disaggregator2/data/documents/";                       
        $path = $root . $documentDir . $document->Filepath;
        
        //now get the text
        exec('java -cp ' . $javaDir . '/pdfbox:' . $javaDir . '/pdfbox/* PDFReader "' . $path . '"', $output);
        
        foreach($output as $page)
        {
            $results[] = new Viewable($page, "page");
        }
        
        return $results;
        
    } 
}

?>


