<?php

class FileValue extends adro implements FieldValue
{
    public static function createFromUpload(iotaStorePage $file, $description, FileValue $filevalue)
    {
        $path = $file->getDriver()->getFilesystemPath();

        $ext = self::getExtension(basename($path));


        // Lookup the location of the store and build a name for the new file
        $aspath = 'site/files';
        $aname = 'f_' . $component->ComponentID . '_' . uniqid() . '.' . $ext;
        $fullpath = iotaPath::makepath($aspath, $aname);        

        $file->move($fullpath);

        // Create the document record
        $filevalue->Name = $description;
        $filevalue->Value = $aname;

        return $filevalue;
    }
    
    public static function getExtension($filename)
    {
        return strtolower(array_pop(explode('.', $filename)));
    } 
    
    public function validate()
    {
        return true;
    }
    
    public function getPreview()
    {
        return $this->Name;
    }
}

