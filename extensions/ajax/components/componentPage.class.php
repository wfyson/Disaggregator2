<?php

class ComponentPage extends tauAjaxXmlTag
{
    private $component;
    
    public function __construct(Component $component = null, DisaggregatorPerson $user = null)
    {
        parent::__construct('div');

        if($component != null)
        {
            //is a user required for permission to view this component
            
            $this->addChild($this->header = new BootstrapHeader($component->getPreviewText(), $component->getDescriptor()->Name));

            $this->component = $component;

            $this->attachEvent('init', $this, 'e_init');
        }
        else
        {
            $this->addChild(new BootstrapAlert("No component selected!", "alert-danger"));
        }
    }

    public function e_init(tauAjaxEvent $e)
    {
        $this->addChild($this->componentViewer = new ComponentViewer());
        $this->componentViewer->showComponent($this->component);
        $this->componentViewer->addClass("col-md-6");
    }
    

}



?>
