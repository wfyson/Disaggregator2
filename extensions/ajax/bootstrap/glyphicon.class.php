<?php

class Glyphicon extends tauAjaxSpan
{
	public function __construct($icon)
	{
		parent::__construct();	
                $this->addClass("glyphicon");
		$this->addClass("glyphicon-$icon");
	}
}

?>
