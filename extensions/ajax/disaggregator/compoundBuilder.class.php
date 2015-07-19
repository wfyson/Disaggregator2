<?php

/*
 * Presents the user with an interface for creating a record based on the given 
 * descriptor.
 */
class CompoundBuilder extends tauAjaxPager
{

    private $person;
    private $document;
    private $descriptor;

    public function __construct(DisaggregatorPerson $person, Document $document=null, Descriptor $descriptor=null)
    {
	parent::__construct();

        error_log($descriptor->Name);
        
        $this->person = $person;
        $this->document = $document;
        $this->descriptor = $descriptor;

        $this->init();                        
    }
        
    public function init()
    {      
        //create a page for each of the descriptor's fields
        $descriptorFields = $this->descriptor->getdescriptorfields();
        $i = $descriptorFields->getIterator();
        while($i->hasNext())
        {
            $descriptorField = $i->next();
            $field = $descriptorField->getDisaggregatorField();
            $this->addBuilderStage($field);
        }
        
        $this->toPage(0);
    }    
    
    public function addBuilderStage(Field $field)
    {
        switch($field->Type)
        {
            case "Text":
                $this->addPage(new TextStage($field));
                break;
            case "File":
                $this->addPage(new FileStage($field));
                break;
            case "Compound":
                $this->addPage(new CompoundStage($field));
                break;
        }
    }
}



?>
