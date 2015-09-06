<?php

/*
 * Display a document to be redacted
 */
class RedactorViewer extends DocumentViewer
{        
    public function __construct(DisaggregatorPerson $person, Document $document)
    {
        parent::__construct($person, $document);
        
        $this->addClass("DocumentViewer");
                        
        $this->attachEvent("onclick", $this, "e_stop_redacting");
    }
   
    public function e_stop_redacting(tauAjaxEvent $e)
    {
        $this->triggerEvent("stop_redacting");
    }
    
    public function e_show_document(tauAjaxEvent $e)
    {                   
        $viewables = $this->document->prepareForViewer();
        
        $this->setData('');
        
        //add the highlight selector
        $this->addChild($this->highlighter = new tauAjaxXmlTag('div'));
        $this->highlighter->setAttribute("id", "highlighter");  
        
        //show the document content
        foreach($viewables as $viewable)
        {
            $this->addChild($viewable);
            $viewable->initRedact();
        }        
    }
    
    
    
    
}

?>
