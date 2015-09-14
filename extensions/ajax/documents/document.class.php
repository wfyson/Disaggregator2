<?php

class DocumentUI extends tauAjaxXmlTag
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
        //$this->addChild($this->documentEditor = new DocumentEditor($this>document));
        $this->addChild(new BootstrapHeader($this->document->Name));        
        
        if($e->getParam("saved"))
        {
            $this->addChild(new BootstrapAlert("Document Saved", "alert-success"));
        }
        
        $this->addChild($this->record = new TauAjaxADRORecord(DisaggregatorModel::get()->document));
        $this->record->addClass("col-md-6");
        
        $editable = array('Name', 'Security');
        $this->record->ignore();
        foreach($editable as $e)
        {
            $this->record->ignore($e, false);
        }
        
        $this->record->show($this->document);
        
        //styling for the editor
        $this->record->runJS("                
            $('.tauAjaxTextInput').addClass('form-control');
            $('.tauAjaxSelect').addClass('form-control');                 
            $('button').addClass('btn btn-primary');
        ");
        
        $this->record->attachEvent('save', $this, 'e_saved');
    }             
    
    public function e_document_select(tauAjaxEvent $e)
    {
        $this->document = $e->getParam("document");
        $this->triggerEvent("init");
    }     
    
    public function e_saved(tauAjaxEvent $e)
    {
        $this->triggerEvent("init", array("saved"=>true));
    }
}



?>
