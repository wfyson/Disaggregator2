<?php

class HelperUtil
{
    public static function addHelpGlyph($parent, $position, $text)
    {
        $parent->addChild($help = new Glyphicon("question-sign"));
        
        $help->addClass("help");
        $help->setAttribute('data-container', "body");
        $help->setAttribute('data-toggle', "popover");
        $help->setAttribute('data-html', "true");
        $help->setAttribute('data-placement', $position);
        $help->setAttribute('data-content', $text);                        
    }
    
    public static function initHelpGlyph($host)
    {
        $host->runJS("                
            $('.help').popover();        
        ");
    }
}

?>
