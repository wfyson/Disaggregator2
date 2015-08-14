<?php

class Document extends adro 
{
    public static function createFromUpload(iotaStorePage $file, $description, DisaggregatorPerson $person)
    {
        $path = $file->getDriver()->getFilesystemPath();

        $ext = self::getExtension(basename($path));


        // Lookup the location of the store and build a name for the new file
        $aspath = 'site/documents';
        $aname = 'd_' . $person->UserID . '_' . uniqid() . '.' . $ext;
        $fullpath = iotaPath::makepath($aspath, $aname);        

        $file->move($fullpath);

        // Create the document record
        $rec = $person->getModel()->document->getNew();
        $rec->UserID = $person->UserID;
        $rec->Name = $description;
        $rec->Filepath = $aname;
        $rec->Source = "Upload";
        $rec->Security = "User";
        $rec->save();

        return $rec;
    }

    public static function getExtension($filename)
    {
        return strtolower(array_pop(explode('.', $filename)));
    } 

    public function getFullPath()
    {
        $conf = iotaConf::getInstance();
        return iotaPath::makePath('?f=documents', $this->Filepath);
    }
    
    public function prepareForViewer()
    {
        //establish how we need to read the document and return a list of viewable objects
        switch(Document::getExtension($this->Filepath))
        {
            case "docx": 
                return WordReader::read($this);
                break;
            case "pdf":
                return PDFReader::read($this);
                break;
        }        
    }
    
    public function getCompleteComponents()
    {                
        $components = $this->getcomponents();        
        $complete = Component::getComplete($components);   
        
        return $complete;
    }
    
    public function getIncompleteComponents()
    {                
        $components = $this->getcomponents();        
        $complete = Component::getIncomplete($components);           
        
        return $complete;
    }
}
