<?php

/*
 * Display a document, be it a Word or PDF document...
 */
class DocumentViewer extends tauAjaxXmlTag
{

    private $person;
    private $document;

    public function __construct(DisaggregatorPerson $person, Document $document=null)
    {
	parent::__construct('div');

        $this->person = $person;
        $this->document = $document;

        $this->init();                        
    }
        
    public function init()
    {                     
        $viewables = $this->document->prepareForViewer();
        
        foreach($viewables as $viewable)
        {
            $this->addChild($viewable);
        }
        
        $this->addChild($this->highlighter = new tauAjaxXmlTag('div'));
        $this->highlighter->setAttribute("id", "highlighter");
        
        $this->runJS('
            $(".content").mouseup(function(e)
            {
                var selection = getSelectionText();
                if(selection.length > 0)
                {
                    $("#highlighter").data("value", selection);

                    //text has been selected
                    var x = e.pageX + 10;
                    var y = e.pageY - 20;
                    $("#highlighter").css({"top": y, "left": x}).show();
                }
                else
                {
                    //no text has been selected
                    $("#highlighter").hide();
                    $("#highlighter").data("value", "");
                }
            });
            
            $("#highlighter").click(function(){
                $(this).hide();
                var selection = $(this).data("value");
                console.log(selection);
                $input = $(".CompoundBuilder .BuilderStage input");
                $input.val(selection);
                $input.attr("value", selection);
                
            });
            
            function getSelectionText()
            {
                var text = "";
                if (window.getSelection)
                {
                    text = window.getSelection().toString();
                }
                else
                {
                    if(document.selection && document.selection.type != "Control")
                    {
                        text = document.selection.createRange().text;
                    }
                }            
                return text;
            };
        ');
    }            
}



?>
