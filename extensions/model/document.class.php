<?php

//iotaConf::getInstance()->setDefault('DataDir', 'sites/disaggregator/data/documents/');

//iotaConf::getInstance()->alias('DocumentDir', 'DataDir');
//iotaConf::getInstance()->setDefault('DataDir', 'site/documents/');



class Document extends adro 
{
	public static function createFromUpload(iotaStorePage $file, $description, DisaggregatorPerson $person)
	{
        	$path = $file->getDriver()->getFilesystemPath();  
        
		$ext = self::getExtension(basename($path));
        
        
        	// Lookup the location of the store and build a name for the new file
	        $aspath = 'site/documents';
	        $aname = 'd_'.$person->UserID.'_'.uniqid().'.'.$ext;
	        $fullpath = iotaPath::makepath($aspath, $aname);;        

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
        return iotaPath::makePath('site/documents', $this->Filepath);
    }
}
