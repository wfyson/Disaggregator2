<?php

class BootstrapPanel extends tauAjaxXmlTag
{
	public function __construct($heading, $class=null)
	{
		parent::__construct("div");		
		$this->addClass("panel");
                
                if(isset($class))
                    $this->addClass($class);
                
                $this->addChild($this->heading = new tauAjaxXmlTag('div'));
                $this->heading->addClass("panel-heading");
                $this->heading->setData($heading);
                
                $this->addChild($this->body = new tauAjaxXmlTag('div'));
                $this->body->addClass("panel-body");
	}
        
        public function addBody(tauAjaxXmlTag $body)
        {
            $this->body->addChild($body);
        }
                
}

?>
