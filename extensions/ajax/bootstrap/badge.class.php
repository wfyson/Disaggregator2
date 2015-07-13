<?php

class BootstrapBadge extends tauAjaxSpan
{
	public function __construct($text)
	{
		parent::__construct($text);		
		$this->addClass("badge");
	}
}

?>
