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
}

?>


