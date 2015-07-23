<?php

class DocumentSelector extends tauAjaxXmlTag
{
    private $person;
    private $selected;
    
    public function __construct(DisaggregatorPerson $person)
    {
	parent::__construct('div');       
        
        $this->person = $person;

        $this->init();                        
    }
        
    public function init()
    {    
        $this->addChild(new BootstrapAlert("No document has been select! Please select or import a document.", "alert-danger"));
        
        $this->addChild($this->interface = new tauAjaxXmlTag('div'));
        
        //show the documents the user has access to        
        $this->interface->addChild($this->existingDocuments = new tauAjaxXmlTag('div'));
        $this->existingDocuments->addClass("col-md-5");
        
        $this->existingDocuments->addChild(new tauAjaxHeading(2, "Select a document"));
        
        $this->existingDocuments->addChild($this->documentList = new DocumentListGroup());        
        $documents = $this->person->getdocuments();
        $this->documentList->showDocuments($documents);
        $this->attachEvent('select_document', $this, 'e_existing');
        
        //upload a document
        $this->interface->addChild($this->importDocument = new tauAjaxXmlTag('div'));
        $this->importDocument->addClass("col-md-5 col-md-offset-2");
        
        $this->importDocument->addChild(new tauAjaxHeading(2, "Import a document"));
        
        //select a document  
        $this->addChild($this->controls = new tauAjaxXmlTag('div'));
        $this->controls->addClass("col-md-12");
        $this->controls->addChild($this->btn_select = new BootstrapButton("Select", "btn-primary"));
        $this->btn_select->attachEvent("onclick", $this, "e_select");
    }            
    
    public function e_existing(tauAjaxEvent $e)
    {
        $this->selected = $e->getParam('document');
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        $this->triggerEvent("document_select", array("document"=> $this->selected));
    }
}

class DocumentListGroup extends ListGroup
{
    
    public function showDocuments(ADROSet $documents)
    {
        $this->setData('');

        $i = $documents->getIterator();
        while ($i->hasNext())
        {
            $document = $i->next();
            $this->addDocument($document);
        }
    }
    
    public function addDocument(Document $document)
    {
        $this->addChild(new DocumentListGroupItem($document));
    }
    
}

class DocumentListGroupItem extends ListGroupItem
{
    private $document;
    
    public function __construct(Document $document)
    {
        parent::__construct($document->Name);
        
        $this->document = $document;
        
        $this->attachEvent("onclick", $this, "e_select");
    }
    
    public function e_select(tauAjaxEvent $e)
    {
        $this->runJS("$('.active').removeClass('active');");
        
        $this->addClass("active");
        $this->triggerEvent("select_document", array("document" => $this->document));                
    }
}



?>
