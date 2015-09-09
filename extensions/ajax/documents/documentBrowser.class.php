<?php

class DocumentBrowser extends tauAjaxXmlTag
{
	private $person;
	private $documentList;

	public function __construct(DisaggregatorPerson $person)
	{
            parent::__construct('div');

            $this->person = $person;

            $this->addChild(new tauAjaxHeading(2, 'My Documents'));
			
            $this->attachEvent('init', $this, 'e_init');
            $this->attachEvent('refresh', $this, 'e_refresh');
	}

	public function e_init(tauAjaxEvent $e=null)
	{	
            $this->addChild($this->up = new TauAjaxUpload(4096, 'Upload Document', ''));
            $this->up->attachEvent('uploadcomplete', $this, 'e_uploaded');

            $this->addChild($this->documentList = new DocumentList());                                    
            
            $this->triggerEvent('refresh');
		
            //styling for the button
            $this->up->runJS("
		$('button').addClass('btn btn-primary');
            ");
	}

	public function e_uploaded(tauAjaxEvent $e)
    	{
        	// We catch the event that we throw - It needs to be ignored!
	        if($e->getParam('attachment') !== false)
		{						
			return;
		}        

	        $file = $e->getParam('file');
	        $name = $e->getParam('name');
        
	        $e->disableBubble();
        
	        $att = Document::createFromUpload($file,  $name, $this->person);        	       
        
	        // Add the attachment as a parameter on the event and re-trigger it
	        $this->triggerEvent('uploadcomplete', array('file'=>$file, 'name'=>$name, 'attachment'=>$att));
		$this->triggerEvent('refresh');
    }
	
	public function e_refresh(tauAjaxEvent $e)
	{
		$this->person->flushRelations();
		$documents = $this->person->getdocuments();	
		$this->documentList->showDocuments($documents);
	}
	

}

class DocumentList extends BootstrapTable
{
	public function showDocuments(ADROSet $documents)
	{
		$this->body->setData('');		

		$i = $documents->getIterator();
		while($i->hasNext())
		{		
			$doc = $i->next();
			$this->addDoc($doc);
		}
	}

	public function addDoc(Document $doc)
	{
		return $this->body->addChild(new DocumentRow($doc));
	}
	
}

class DocumentRow extends tauAjaxXmlTag
{
	private $document;

	public function __construct(Document $document)
	{
            parent::__construct("tr");

            $this->document = $document;
                  
            //name
            $this->addChild($this->cell_name = new tauAjaxXmlTag("td"));
            $this->cell_name->addChild($this->span_name = new tauAjaxSpan("$document->Name "));
            $this->span_name->addClass("h4");
            if($this->document->Security == "User" || $this->document->Security == "Contributors")
                $this->span_name->addChild(new Glyphicon("lock"));
            else
                $this->span_name->addChild(new Glyphicon("eye-open"));
                                   
            //incomplete components
            $this->addChild($this->cell_incomplete = new tauAjaxXmlTag("td"));
            $this->cell_incomplete->addChild($this->btn_incomplete = new BootstrapLinkButton("Progress ", "?f=overview&document=$document->DocumentID&tab=progress", "btn-warning"));
                
            $incomplete = $this->document->getIncompleteComponents();
            if(count($incomplete) == 0)
            {
                $this->btn_incomplete->addClass("disabled");
            }
            else
            {
                $this->btn_incomplete->addChild(new BootstrapBadge(count($incomplete)));
            }
            
            //complete components
            $this->addChild($this->cell_components = new tauAjaxXmlTag("td"));
            $this->cell_components->addChild($this->btn_components = new BootstrapLinkButton("Components ", "?f=overview&document=$document->DocumentID&tab=complete", "btn-success"));
                
            $components = $this->document->getCompleteComponents();
            if(count($components) == 0)
            {
                $this->btn_components->addClass("disabled");
            }
            else
            {
                $this->btn_components->addChild(new BootstrapBadge(count($components)));
            }
                
            //disaggregator
            $this->addChild($this->cell_disaggregator = new tauAjaxXmlTag("td"));
            $this->cell_disaggregator->addChild($this->btn_disaggregator = new BootstrapSplitButton("Disaggregate", "btn-primary"));                
            $this->btn_disaggregator->btn->attachEvent("onclick", $this, "e_disaggregate");    
            
            $model = DisaggregatorModel::get();
            $descriptors = $model->descriptor->getRecords();
            $i = $descriptors->getIterator();
            while($i->hasNext())
            {
                $descriptor = $i->next();
                $this->btn_disaggregator->addItem(new tauAjaxLink($descriptor->Name, "./?f=disaggregator&document=" . $this->document->DocumentID . "&descriptor=$descriptor->DescriptorID"));
            }
            
            //scanner
            $this->addChild($this->cell_scanner = new tauAjaxXmlTag("td"));            
            $this->cell_scanner->addChild($this->btn_scanner = new BootstrapSplitButton("Scanner", "btn-primary"));                
            $this->btn_scanner->btn->attachEvent("onclick", $this, "e_scanner");    

            $i = $descriptors->getIterator();
            while($i->hasNext())
            {
                $descriptor = $i->next();
                $this->btn_scanner->addItem(new tauAjaxLink($descriptor->Name, "./?f=scanner&document=" . $this->document->DocumentID . "&descriptor=$descriptor->DescriptorID"));
            }
            
            //redactor
            $this->addChild($this->cell_redactor = new tauAjaxXmlTag("td"));
            if($this->document->canBeRedacted())
            {
                $this->cell_redactor->addChild($this->btn_redactor = new BootstrapLinkButton("Redactor ", "?f=redactor&document=$document->DocumentID", "btn-primary"));
            }
	}                
        
        public function e_disaggregate(tauAjaxEvent $e)
        {
            $this->runJS(
                "
                    window.location.href = './?f=disaggregator&document=" . $this->document->DocumentID . "';
                ");
        }
        
        public function e_scanner(tauAjaxEvent $e)
        {            
            $this->runJS(
                "
                    window.location.href = './?f=scanner&document=" . $this->document->DocumentID . "';
                ");
        }

}

?>
