<?php

class Viewable extends tauAjaxXmlTag
{
    public function __construct($content, $xpath, $style, $styleValue=null)
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
        switch($this->style)
        {
            case "para":
                $this->addChild($this->content = new tauAjaxParagraph($this->content));
                break;
            case "heading":
                $this->addChild($this->content = new tauAjaxHeading($this->styleValue, $this->content));
                break;
             case "image":                      
                $this->addChild($this->content = new tauAjaxImage($this->content));
                $this->content->addClass("image");
                break;
            case "caption":
                $this->addChild($this->content = new tauAjaxParagraph($this->content));
                $this->content->addClass("caption");
                break;            
        }
        $this->content->addClass("content");
    }        
}

?>


