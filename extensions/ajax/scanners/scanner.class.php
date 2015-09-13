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
        
        $this->attachEvent('save', $this, 'e_save');
        $this->attachEvent('remove', $this, 'e_remove');
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
    
    public function e_remove(tauAjaxEvent $e)
    {
        $this->deleteChild($e->getParam('result'));
    }
    
    public function e_save(tauAjaxEvent $e)
    {
        $model = DisaggregatorModel::get();
        $scannerResult = $e->getParam('result');
                 
        //svae the component
        $component = $model->component->getNew();
        $component->DescriptorID = $this->descriptor->DescriptorID;
        $component->DocumentID = $this->document->DocumentID; 
        $component->Source = "scanner";
        $component->save();        
        
        //now save the values
        foreach($scannerResult as $fieldID => $value)
        {
            $field = $model->field->getRecordByPK($fieldID);
            $field->saveFieldValue($value, $component->ComponentID);
        }
    }
}

//present a result from the scanner with an option to save or discard
class ScannerResultRow extends tauAjaxXmlTag
{
    private $scannerResult;
    private $saved = false;
    
    public function __construct($scannerResult)
    {
        parent::__construct("tr");
        
        $this->scannerResult = $scannerResult;        
        
        $this->init();
    }
    
    public function init()
    {
        //preview                
        $model = DisaggregatorModel::get();
        $preview = "";
        $count = 0;
        foreach($this->scannerResult as $fieldID => $value)
        {
            $field = $model->field->getRecordByPK($fieldID);
            $preview .= "$field->Name: $value";
            
            $count++;
            if($count < count($this->scannerResult))
            {
                $preview .= "; ";
            }
        }        
        $this->addChild($this->cell_preview = new tauAjaxXmlTag("td"));
        $this->cell_preview->addChild($this->span_preview = new tauAjaxSpan($preview ));
        $this->span_preview->addClass("h4");
        
        //save button
        $this->addChild($this->cell_save = new tauAjaxXmlTag("td"));
        $this->cell_save->addChild($this->btn_save = new BootstrapButton("Save ", "btn-primary"));
        $this->btn_save->addChild(new Glyphicon("save"));
        $this->btn_save->attachEvent("onclick", $this, "e_save");
        
        //discard button        
        $this->addChild($this->cell_discard = new tauAjaxXmlTag("td"));
        $this->cell_discard->addChild($this->btn_discard = new BootstrapButton("Discard  ", "btn-danger"));
        $this->btn_discard->addChild(new Glyphicon("remove"));
        $this->btn_discard->attachEvent("onclick", $this, "e_discard");
    }
    
    public function e_save(tauAjaxEvent $e)
    {
        //save the component and any values we have for it
        $this->triggerEvent("save", array("result" => $this->scannerResult));
        
        //adjust entry to indicate result is saved
        $this->saved = true;
        $this->deleteChild($this->cell_save);
        $this->deleteChild($this->cell_discard);
        
        $this->addChild($this->cell_saved = new tauAjaxXmlTag("td"));
        $this->cell_saved->addChild($this->btn_saved = new BootstrapButton("Saved ", "btn-success"));
        $this->btn_saved->addChild(new Glyphicon("ok"));
        $this->btn_saved->addClass("saved");
        
        $this->addChild($this->cell_discard = new tauAjaxXmlTag("td"));
    }
    
    public function e_discard(tauAjaxEvent $e)
    {
        $this->triggerEvent("remove", array("result" => $this));
    }
}





?>
