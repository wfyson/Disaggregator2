<?php

class RedactorUI extends tauAjaxXmlTag
{

    private $person;
    private $document;
    private $redacting = null;
    private $redactions = array();

    public function __construct(DisaggregatorPerson $person, Document $document=null)
    {
	parent::__construct('div');

        $this->person = $person;
        $this->document = $document;

        $this->attachEvent('init', $this, 'e_init');   
        $this->attachEvent('document_select', $this, 'e_document_select');      
        
        $this->attachEvent("start_redacting", $this, "e_start_redacting");
        $this->attachEvent("stop_redacting", $this, "e_stop_redacting");
        $this->attachEvent("save_redaction", $this, "e_save_redaction");
    }
        
    public function e_init(tauAjaxEvent $e)
    {      
        $this->setData("");
            
        //do we have a document?
        if(!isset($this->document) && Document::getExtension($this->document->Fullpath == "docx"))
        {
            $this->addChild($this->documentSelector = new DocumentSelector($this->person, true));
            return;
        }
            
        //yes we have everything        
        $this->addChild($this->redactorControls = new RedactorControls($this->person, $this->document));
        $this->addChild($this->redactorViewer = new RedactorViewer($this->person, $this->document));   
    }             
    
    public function e_document_select(tauAjaxEvent $e)
    {
        $this->document = $e->getParam("document");
        $this->triggerEvent("init");
    }   
    
    public function e_start_redacting(tauAjaxEvent $e)
    {
        //get the viewable we now redacting        
        $redacting = $e->getParam("viewable");
        
        //stop the redaction process on any previous viewable
        if($this->redacting !== null)
        {                       
            $this->redacting->stop_redacting();
        }
        
        $redacting->start_redacting();
        $this->redacting = $redacting;               
    }
    
    public function e_stop_redacting(tauAjaxEvent $e)
    {
        //stop the redaction process on any previous viewable
        if($this->redacting !== null)
        {                       
            $this->redacting->stop_redacting();
            
            //if the item has been redacted, add it to the list of redactions
            if($this->redacting->redacted)
            {
                $this->redactions[] = $this->redacting;
            }
        }
    }
    
    public function e_save_redaction(tauAjaxEvent $e)
    {
        $this->triggerEvent("stop_redacting");
        
        if(count($this->redactions) > 0)
        {
            //only word documents can be redacted at the moment
            $new = WordWriter::write($this->person, $this->document, $this->redactions);
        
            //create a new record for the new document
            $rec = $this->person->getModel()->document->getNew();
            $rec->UserID = $this->person->UserID;
            $rec->Name = "redacted_" . $this->document->Name;
            $rec->Filepath = $new;
            $rec->Source = "Redacted";
            $rec->Security = "User";
            $rec->ParentID = $this->document->DocumentID;
            $rec->save();
            
            //show new document
            $this->redactorControls->showRedacted($rec);
        }
        
    }
}



?>
