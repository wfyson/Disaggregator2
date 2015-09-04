<?php

class RedactorUI extends tauAjaxXmlTag
{

    private $person;
    private $document;

    public function __construct(DisaggregatorPerson $person, Document $document=null)
    {
	parent::__construct('div');

        $this->person = $person;
        $this->document = $document;

        $this->attachEvent('init', $this, 'e_init');   
        $this->attachEvent('document_select', $this, 'e_document_select');        
    }
        
    public function e_init(tauAjaxEvent $e)
    {      
        $this->setData("");
            
        //do we have a document?
        if(!isset($this->document))
        {
            $this->addChild($this->documentSelector = new DocumentSelector($this->person, true));
            return;
        }
            
        //yes we have everything        
        $this->addChild($this->documentViewer = new DocumentViewer($this->person, $this->document));   
    }             
    
    public function e_document_select(tauAjaxEvent $e)
    {
        $this->document = $e->getParam("document");
        $this->triggerEvent("init");
    }   
}



?>
