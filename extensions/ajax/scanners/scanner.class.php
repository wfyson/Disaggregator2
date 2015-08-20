<?php

class ScannerUI extends tauAjaxXmlTag
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
        
        $this->attachEvent('run_scan', $this, 'e_run_scan');
        $this->attachEvent('show_results', $this, 'e_show_results');
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
        
        //do we have a descriptor
        if(!isset($this->descriptor))
        {
            $this->addChild($this->descriptorSelector = new DescriptorSelector($this->person));
            return;
        }
            
        //yes we have everything                        
        $intro = "Select a scanner to look for " . $this->descriptor->Name . "s in " . $this->document->Name;
        $this->addChild(new BootstrapJumbotron("Scanner", $intro));
        
        $this->addChild($this->scannerView =new tauAjaxXmlTag('div'));
        
        $this->scannerView->addChild($this->scannerList = new ScannerList($this->descriptor)); 
        $this->scannerList->showScanners($this->descriptor->getscanners());
                
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
    
    public function e_run_scan(tauAjaxEvent $e)
    {
        $this->scannerView->setData("");
        $this->scannerView->addChild(new Loader());
    
        $this->scanner = $e->getParam("scanner");
        
        $this->triggerDelayedEvent(0.5, "show_results");
    }
    
    public function e_show_results(tauAjaxEvent $e)
    {                       
        $results = $this->scanner->runScan($this->document);                
        
        $this->scannerView->setData("");
        $this->scannerView->addChild($resultsTable = new BootstrapTable());
        foreach($results as $result)
        {
            $resultsTable->body->addChild(new ScannerResultRow($result));
        }
    }
}

//present a result from the scanner with an option to save or discard
class ScannerResultRow extends tauAjaxXmlTag
{
    private $scannerResult;
    
    public function __construct($scannerResult)
    {
        parent::__construct("tr");
        
        $this->scannerResult = $scannerResult;        
        
        $this->init();
    }
    
    public function init()
    {
        $this->addChild($this->cell_save = new tauAjaxXmlTag("td"));
        $this->cell_save->addChild($this->btn_save = new BootstrapButton("Save ", "btn-primary"));
        $this->btn_save->addChild(new Glyphicon("save"));
        
        $this->addChild($this->cell_discard = new tauAjaxXmlTag("td"));
        $this->cell_discard->addChild($this->btn_discard = new BootstrapButton("Discard  ", "btn-danger"));
        $this->btn_discard->addChild(new Glyphicon("remove"));
    }
}





?>
