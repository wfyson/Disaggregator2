<?php

class BootstrapTable extends tauAjaxXmlTag
{
	public function __construct()
	{
		parent::__construct("table");
                
                $this->addClass("table table-striped");
                
                $this->addChild($this->head = new tauAjaxXmlTag("thead"));
                $this->addChild($this->body = new tauAjaxXmlTag("tbody"));
	}
}

?>
