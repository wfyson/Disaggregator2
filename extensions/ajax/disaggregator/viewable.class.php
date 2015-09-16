<?php

 class Viewable extends tauAjaxXmlTag
{
    public function __construct($content, $style, $styleValue=null, $xpath=null)
    {        
        parent::__construct('div');
        
        $this->content = $content;
        $this->xpath = $xpath;
        $this->style = $style;
        
        if(isset($styleValue))
        {
            $this->styleValue = $styleValue;
        }
        
        $this->init();
    }
    
    public function init()
    {        
        $this->setData("");
        
        $this->setAttribute("path", $this->xpath);
        
        if($this->redacted)
        {
            $this->addClass("redacted");
        }
        
        switch($this->style)
        {
            case "para":
                $this->addChild($this->contentView = new tauAjaxParagraph($this->content));
                break;
            case "heading":
                $this->addChild($this->contentView = new tauAjaxHeading($this->styleValue, $this->content));
                break;
             case "image":                      
                $this->addChild($this->contentView = new tauAjaxImage($this->content));
                $this->contentView->addClass("image");
                break;
            case "caption":
                $this->addChild($this->contentView = new tauAjaxParagraph($this->content));
                $this->contentView->addClass("caption");
                break;  
            case "page":
                $this->addChild($this->contentView = new tauAjaxParagraph($this->content));
                $this->contentView->addClass("page well");
                
                break;
        }
        $this->contentView->addClass("content");
    }  
    
    public function getStyle()
    {
        return $this->style;
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function initRedact()
    {
        $this->attachEvent("onclick", $this, "e_start_redact");
    }

    public function e_start_redact(tauAjaxEvent $e)
    {
        $e->disableBubble();
        
        if(!$this->redacting)
            $this->triggerEvent("start_redacting", array("viewable"=>$this));
    }
    
    public function start_redacting()
    {   
        //store the fact we are redacting this viewable        
        $this->redacting = true;
        $this->addClass("redacting");
        
        switch($this->style)
        {
            case "para":
                $this->paraRedacting();
            case "heading":
                $this->paraRedacting();
            case "caption":
                $this->paraRedacting();
        }
    }
    
    public function stop_redacting()
    {
        if($this->redacting)
        {
            //no longer redacting
            $this->redacting = false;
            $this->removeClass("redacting");
            
            //get the new contents
            $newContent = $this->textArea->getValue();
            
            //if different, store new content
            if($newContent != $this->content)
            {
                $this->content = $newContent;
                $this->redacted = true;
            }
            
            $this->init();
        }
    }
    
    //set the viewable up for redacting paragraphs
    public function paraRedacting()
    {
        //keep the height of the previously displayed paragraph
        $this->runJS("
            var height = $(_target).height();
            $(_target).css('height', height+10);
        ");
        
        //remove the inital contents and replace 
        $this->setData("");
        
        //replace with an editable version
        $this->addChild($this->textArea = new tauAjaxTextarea());
        
        $this->textArea->setValue($this->content);
    }
}

?>


