<?php

class DocumentEntry extends tauAjaxListItem
{

	private $document;

	public function __construct(Document $document)
	{
		parent::__construct();

		$this->document = $document;

		$this->addChild(new tauAjaxHeading(3, $document->Name);

	}

}


?>
