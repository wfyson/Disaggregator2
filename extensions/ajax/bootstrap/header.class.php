<?php

class BootstrapHeader extends tauAjaxXmlTag
{
	public function __construct($heading, $subheading)
	{
		parent::__construct('div');
		
		$this->addClass("page-header");
                $this->addChild($this->header = new tauAjaxHeading(1, $heading));
                
                if(isset($subheading))
                {
                    $this->header->addChild($this->subheading = new tauAjaxXmlTag('small'));
                    $this->subheading->setData($subheading);
                }
	}
}

?>
