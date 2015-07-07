<?php

class Jumbotron extends tauAjaxXmlTag
{
	public function __construct($heading)
	{
		parent::__construct('div');
		
		$this->addClass("jumbotron");

		$this->addChild(new tauAjaxHeading(2, $heading));
	}
}

?>
