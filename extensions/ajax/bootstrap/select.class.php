<?php

class BootstrapSelect extends tauAjaxSelect
{
	public function __construct()
	{
            parent::__construct();
            
            $this->addClass("form-control");
	}
}

?>
