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
    
    public function getDownloadPath()
    {
        $conf = iotaConf::getInstance();
        return iotaPath::makePath($this->Filepath);
    }
    
    public function prepareForViewer()
    {
        //establish how we need to read the document and return a list of viewable objects
        switch(Document::getExtension($this->Filepath))
        {
            case "docx": 
                return WordReader::read($this);
                break;
            case "pptx":
                return PowerpointReader::read($this);
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
    
    public function getPlainText()
    {
        //derive plain text path
        $documentDir = "sites/Disaggregator2/data/documents/";
        $textPath = $documentDir . substr($this->Filepath, 0, strpos($this->Filepath, ".")) . ".txt";
        
        if (!file_exists($textPath)) //then write it...
        {
            $viewables = $this->prepareForViewer();
            $plainText = array();
            foreach($viewables as $viewable)
            {
                if(in_array($viewable->getStyle(), array("para", "page")))
                {
                    $plainText[] = $viewable->getContent() . PHP_EOL;
                }
            }            
            file_put_contents($textPath, $plainText);
	}
        return $textPath;
    }
    
    public function canBeRedacted()
    {
        if(self::getExtension($this->Filepath) == "docx")
        {
            return true;
        }
        else
            return false;
    }
    
    public function changeSecurity()
    {
        if($this->Security == "User")
            $this->Security = "Public";
        elseif($this->Security == "Public")
            $this->Security = "User";
        
        $this->save();
    }
}
