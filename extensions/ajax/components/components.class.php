<?php

class ComponentUI extends tauAjaxXmlTag
{

	private $person;

	public function __construct(DisaggregatorPerson $person)
	{
		parent::__construct('div');

		$this->person = $person;

		$this->addChild($this->jumbo = new BootstrapJumbotron("Components"));
		$this->jumbo->addChild(new tauAjaxParagraph("Components describe the discrete chunks of information that may be extracted
        from a document. They can be used to describe any piece of data that may 
        provide valuable information even away from its parent document."));                                
                
                $this->attachEvent('init', $this, 'e_init');
	}
        
        public function e_init(tauAjaxEvent $e)
        {
            $this->jumbo->addChild($this->btn_component = new BootstrapButton("Add New Component", "btn-success"));
            $this->btn_component->attachEvent("onclick", $this, "e_component");
            
            $this->addChild($this->descriptorBrowser = new DescriptorBrowser($this->person));
            
            $this->addChild($this->descriptorCreator = new DescriptorCreator($this->person));
            $this->descriptorCreator->addClass("hide");
        }
        
        public function e_component(tauAjaxEvent $e)
        {            
            $this->descriptorBrowser->addClass("hide");
            $this->descriptorCreator->removeClass("hide");
        }

}



?>
