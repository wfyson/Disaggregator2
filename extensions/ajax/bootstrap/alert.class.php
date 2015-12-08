<?php

/*
 * $class = alert-success | alert-info | alert-warning | alert-danger
 */

class BootstrapAlert extends tauAjaxXmlTag
{
	public function __construct($text, $class=null)
	{
		parent::__construct("div");
                $this->addClass("alert");
                $this->setAttribute("role", "alert");
                $this->setData($text);
                
                if(isset($class))
                    $this->addClass($class);
	}
}

?>
