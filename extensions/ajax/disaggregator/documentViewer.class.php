<?php

/*
 * Display a document, be it a Word or PDF document...
 */
class DocumentViewer extends tauAjaxXmlTag
{

    private $person;
    private $document;
    private $descriptor;

    public function __construct(DisaggregatorPerson $person, Document $document=null)
    {
	parent::__construct('div');

        $this->person = $person;
        $this->document = $document;

        $this->attachEvent('init', $this, 'e_init');                        
    }
        
    public function e_init(tauAjaxEvent $e)
    {               
    }            
}



?>
