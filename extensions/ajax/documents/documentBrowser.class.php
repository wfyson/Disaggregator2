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
		$this->addChild($this->up = new TauAjaxUpload(4096, 'Upload Document'));
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

class DocumentList extends tauAjaxList
{
	public function showDocuments(ADROSet $documents)
	{
		$this->setData('');		

		$i = $documents->getIterator();
		while($i->hasNext())
		{		
			$doc = $i->next();
			$this->addDoc($doc);
		}
	}

	public function addDoc(Document $doc)
	{
		return $this->addChild(new DocumentListItem($doc));
	}
	
}

class DocumentListItem extends tauAjaxListItem
{

	private $document;

	public function __construct(Document $document)
	{
		parent::__construct();

		$this->document = $document;

		$this->addChild(new tauAjaxHeading(3, $document->Name));	

	}

}

?>
