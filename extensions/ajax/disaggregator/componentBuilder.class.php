<?php

/*
 * Presents the user with an interface for creating a record based on the given 
 * descriptor.
 */
class ComponentBuilder extends tauAjaxPager
{

    private $person;
    private $document;
    private $descriptor;
    private $component;
    
    public function __construct(DisaggregatorPerson $person, Document $document, Descriptor $descriptor, Component $component=null)
    {
	parent::__construct();

        $this->person = $person;
        $this->document = $document;
        $this->descriptor = $descriptor;
                
        if(isset($component))
        {
            $this->component = $component;
        }
        else
        {
            //create the component we will be building up
            $model = DisaggregatorModel::get();
            $this->component = $model->component->getNew();
            $this->component->DescriptorID = $descriptor->DescriptorID;
            $this->component->DocumentID = $document->DocumentID; 
            $this->component->Source = "disaggregator";
            $this->component->save();
        }            
        $this->init();                        
    }
        
    public function init()
    {              
        //create a page for each of the descriptor's fields
        $descriptorFields = $this->descriptor->getdescriptorfields();
        $i = $descriptorFields->getIterator();
        while($i->hasNext())
        {
            $descriptorField = $i->next();
            $field = $descriptorField->getDisaggregatorField();
            $this->addBuilderStage($this->component, $field);
        }
        $this->addPage(new FinalStage($this->component, $this->descriptor));
        
        //apply styling
        $this->runJS("                
            $('.tauAjaxTextInput').addClass('form-control');
            $('.tauAjaxSelect').addClass('form-control');                 
            $('button').addClass('btn btn-primary');
        ");
        
        $this->toPage(0); 
        
        $this->addChild($this->progress = new BootstrapProgress(100 / count($this->pages)));

        $this->attachEvent("progress", $this, "e_progress");
    }    
    
    public function addBuilderStage(Component $component, Field $field)
    {        
        switch($field->Type)
        {
            case "Text":
                $this->addPage(new TextStage($component, $field));
                break;
            case "File":                
                $this->addPage(new FileStage($component, $field));
                break;
            case "Component":
                $this->addPage(new ComponentStage($component, $field));
                break;
            case "Contributor":
                $this->addPage(new ContributorStage($component, $field));
                break;
        }
    }
    
    public function setValue($value)
    {
        $page = $this->getPage($this->page);
        $page->setValue($value);
    }
    
    public function e_progress(tauAjaxEvent $e)
    {       
        $this->progress->setProgress((($this->page+1) / count($this->pages)) * 100); 
    }       
}

class FinalStage extends tauAjaxBasicPage
{
    private $component;
    private $descriptor;
    
    public function __construct(Component $component, Descriptor $descriptor)
    {
        parent::__construct();
        
        $this->component = $component;
        $this->descriptor = $descriptor;
    }
    
    public function trigger()
    {  
        $this->triggerEvent('progress', array("field"=>$this->field));
        $this->triggerEvent('show_finished');
        
        $this->setData("");
        
        $this->addChild(new tauAjaxHeading(3, "Thank you for recording a " . $this->descriptor->Name . "!"));
        
        //all we do here is check to see if everything is present        
        $complete = $this->component->isComplete();

        if($complete === true)
        {
            $this->addChild(new tauAjaxParagraph("This component can now be published...somehow!"));
        }
        else
        {
            //if it isn't then we prompt the user to go back and check more stuff
            $this->addChild(new tauAjaxParagraph("The following mandatory fields still need some information: " . implode(", ", $complete)));
        }
        
        $this->addChild($this->btn_homepage = new tauAjaxLink("Back to My Documents", "/"));               
        $this->btn_homepage->addClass("btn btn-success");
        
              
    }
    
}



?>
