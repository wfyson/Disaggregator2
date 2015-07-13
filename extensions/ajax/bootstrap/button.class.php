<?php

class BootstrapButton extends tauAjaxXmlTag
{
	public function __construct($text, $class=null)
	{
            parent::__construct("button");		
            $this->addClass("btn");
            $this->setData($text);
                
            if(isset($class))
                $this->addClass($class);
	}
}

?>
