<?php

class BootstrapJumbotron extends tauAjaxXmlTag
{
	public function __construct($heading, $para=null)
	{
            parent::__construct('div');
		
            $this->addClass("jumbotron");

            $this->addChild(new tauAjaxHeading(2, $heading));
                
            if(isset($para))
            {
                $this->addChild(new tauAjaxParagraph($para));
            }
	}
}

?>
