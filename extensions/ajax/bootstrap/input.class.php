<?php

class BootstrapFormGroup extends tauAjaxXmlTag
{
    public function __construct($label, $input)
    {
        parent::__construct("div");
        
        $this->addChild($label);
        $this->addChild($input);
        
        $this->addClass("form-group");
        $label->addClass("control-label");
    }
    
}

class BootstrapTextInput extends tauAjaxTextInput
{
	public function __construct()
	{
		parent::__construct();		
		$this->addClass("form-control");
	}
}

class BootstrapPasswordInput extends tauAjaxPasswordInput
{
	public function __construct()
	{
		parent::__construct();		
		$this->addClass("form-control");
	}
}

?>
