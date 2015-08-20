<?php

interface DocumentScanner
{
    /*
     * Must return a list of associative arrays of Field IDs to values, 
     * one associative array for each resultant component
     */
    public static function read(Document $document);
}

?>