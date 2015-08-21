<?php

class BootstrapLabel extends tauAjaxSpan
{
	public function __construct($text, $class=null)
	{
		parent::__construct($text);		
		$this->addClass("label");
                
                if(isset($class))
                {
                    $this->addClass("label-$class");
                }
                else
                {
                    $this->addClass("label-default");
                }
	}
}

?>
