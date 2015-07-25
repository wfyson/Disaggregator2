<?php

class FileValue extends adro 
{
    public static function createFromUpload(iotaStorePage $file, $description, Component $component, Field $field)
    {
        $path = $file->getDriver()->getFilesystemPath();

        $ext = self::getExtension(basename($path));


        // Lookup the location of the store and build a name for the new file
        $aspath = 'site/files';
        $aname = 'f_' . $component->ComponentID . '_' . uniqid() . '.' . $ext;
        $fullpath = iotaPath::makepath($aspath, $aname);        

        $file->move($fullpath);

        // Create the document record
        $rec = $field->getModel()->filevalue->getNew();
        $rec->ComponentID = $component->ComponentID;
        $rec->FieldID = $field->FieldID;
        $rec->Name = $description;
        $rec->Value = $aname;

        return $rec;
    }
    
    public static function getExtension($filename)
    {
        return strtolower(array_pop(explode('.', $filename)));
    } 
}

