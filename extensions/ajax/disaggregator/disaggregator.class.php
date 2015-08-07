<?php

class DisaggregatorUI extends tauAjaxXmlTag
{

    private $person;
    private $document;
    private $descriptor;

    public function __construct(DisaggregatorPerson $person, Document $document=null, Descriptor $descriptor=null)
    {
	parent::__construct('div');

        $this->person = $person;
        $this->document = $document;
        $this->descriptor = $descriptor;

        $this->attachEvent('init', $this, 'e_init');   
        $this->attachEvent('document_select', $this, 'e_document_select');
        $this->attachEvent('descriptor_select', $this, 'e_descriptor_select');
        
        $this->attachEvent('update_builder', $this, 'e_update_builder');
        
        $this->attachEvent('show_components', $this, 'e_show_components');
    }
        
    public function e_init(tauAjaxEvent $e)
    {      
        $this->setData("");
            
        //do we have a document?
        if(!isset($this->document))
        {
            $this->addChild($this->documentSelector = new DocumentSelector($this->person));
            return;
        }
        
        //do we have a descriptor
        if(!isset($this->descriptor))
        {
            $this->addChild($this->descriptorSelector = new DescriptorSelector($this->person));
            return;
        }
            
        //yes we have everything
        $this->addChild($this->componentBuilder = new ComponentBuilder($this->person, $this->document, $this->descriptor));
        $this->addChild($this->documentViewer = new DocumentViewer($this->person, $this->document));   
        
        $this->addChild($this->componentBrowser = new ComponentBrowser($this->person));
        $this->componentBrowser->addClass("hide");
    }             
    
    public function e_document_select(tauAjaxEvent $e)
    {
        $this->document = $e->getParam("document");
        $this->triggerEvent("init");
    }        
    
    public function e_descriptor_select(tauAjaxEvent $e)
    {
        $this->descriptor = $e->getParam("descriptor");
        $this->triggerEvent("init");
    }
    
    public function e_update_builder(tauAjaxEvent $e)
    {
        $this->componentBuilder->setValue($e->getParam("value"));
    }
    
    public function e_show_components(tauAjaxEvent $e)
    {
        $this->documentViewer->addClass("hide");
        $this->componentBrowser->removeClass("hide");
        
        $this->componentBrowser->showComponents($e->getParam('descriptor'));
    }
}



?>
