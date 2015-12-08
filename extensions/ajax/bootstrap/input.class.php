<?php

class BootstrapFormGroup extends tauAjaxXmlTag
{
    private $class;
    private $glyph = false;
    
    public function __construct($label, $input)
    {
        parent::__construct("div");
        
        $this->addChild($label);
        $this->addChild($input);
        
        $this->addClass("form-group");
        $label->addClass("control-label");
    }
    
    public function addIcon($class, $icon)
    {
        $this->removeClass($this->class);
        $this->class = $class;
        $this->addClass("has-feedback " . $class);
        
        if($this->glyph)
        {
            $this->deleteChild($this->glyph); //won't delete in the removeIcon function for some reason...
        }
        
        $this->addChild($this->glyph = new Glyphicon($icon));
        $this->glyph->addClass("form-control-feedback");
    }
    
    public function removeIcon()
    {        
        $this->removeClass($this->class);
        if($this->glyph)   
        {
            $this->glyph->addClass("hide");
        }
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
