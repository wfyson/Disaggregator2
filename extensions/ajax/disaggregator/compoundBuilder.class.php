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

        $this->person = $person;
        $this->document = $document;
        $this->descriptor = $descriptor;

        $this->attachEvent('init', $this, 'e_init');                        
    }
        
    public function e_init(tauAjaxEvent $e)
    {      
        //create a page for each of the descriptor's fields
        $this->descriptor->getdescriptorfields();
        $i = $descriptorFields->getIterator();
        while($i->hasNext())
        {
            $descriptorField = $i->next();
            $field = $descriptorField->getDisaggregatorField();
            $this->addBuilderStage($field);
        }
    }    
    
    public function addBuilderStage(Field $field)
    {
        switch($field->type)
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
