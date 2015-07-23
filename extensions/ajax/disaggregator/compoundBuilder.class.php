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
        
        //apply styling
        $this->runJS("                
            $('.tauAjaxTextInput').addClass('form-control');
            $('.tauAjaxSelect').addClass('form-control');                 
            $('button').addClass('btn btn-primary');
        ");
        
        $this->toPage(0); 
        
        $this->addChild($this->progress = new BootstrapProgress(100 / count($this->pages)));

        
        $this->attachEvent("progress", $this, "e_progress");
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
            case "Component":
                $this->addPage(new CompoundStage($field));
                break;
        }
    }
    
    public function e_progress(tauAjaxEvent $e)
    {       
        $this->progress->setProgress((($this->page+1) / count($this->pages)) * 100); 
    }
}



?>
