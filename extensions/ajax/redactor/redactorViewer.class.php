<?php

/*
 * Display a document to be redacted
 */
class RedactorViewer extends DocumentViewer
{

    private $person;
    private $document;

    public function __construct(DisaggregatorPerson $person, Document $document=null)
    {
	parent::__construct('div');

        $this->person = $person;
        $this->document = $document;      
        
        $this->init();                        
    }
        
    public function init()
    {   
        $this->addChild(new Loader());                                                
        
        $this->attachEvent('show_document', $this, 'e_show_document');                
        
        $this->triggerDelayedEvent(0.5, "show_document");
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
        }        
    }
}

?>
