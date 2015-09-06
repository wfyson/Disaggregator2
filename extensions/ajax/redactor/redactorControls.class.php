<?php

/*
 * Save the redacted changes
 */
class RedactorControls extends tauAjaxPager
{

    private $person;
    private $document;
    
    public function __construct(DisaggregatorPerson $person, Document $document)
    {
	parent::__construct();

        $this->person = $person;
        $this->document = $document;  
        $this->init();                        
    }
        
    public function init()
    {              
        $this->addChild(new tauAjaxHeading(3, "Redacting " . $this->document->Name));
       
        $this->addChild($this->btn_save = new BootstrapButton("Save Changes", "btn-success"));
        $this->btn_save->attachEvent("onclick", $this, "e_save");                
    }    
        
    public function e_save(tauAjaxEvent $e)
    {
        $this->triggerEvent("save_redaction");
    }
    
    public function showRedacted(Document $document)
    {
        //show a newly redacted document
        $this->setData("");
        
        $this->addChild(new tauAjaxHeading(3, "Redaction Complete!"));
        
        $this->addChild(new BootstrapLinkButton("Back to My Documents", "/", "btn-success"));       
    }
}




?>
