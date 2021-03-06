<?php

/*
 * Display a document, be it a Word or PDF document...
 */
class DocumentViewer extends tauAjaxXmlTag
{

    protected $person;
    protected $document;

    public function __construct(DisaggregatorPerson $person, Document $document=null)
    {
	parent::__construct('div');
        
        $this->person = $person;
        $this->document = $document;      
        
        $this->init();                        
    }
        
    public function init()
    {   
        $this->addChild(new Loader());
                                        
        $this->attachEvent('update', $this, "e_update");
        
        $this->attachEvent('show_document', $this, 'e_show_document');                
        
        $this->triggerDelayedEvent(0.5, "show_document");
        
        //set the height to fill the screen
        $this->runJS("
            var height = $(window).innerHeight();
            $(_target).css('height', height-195);
        ");
    }
    
    public function e_update(tauAjaxEvent $e)
    {
        $this->triggerEvent("update_builder", array("value"=>$e->getParam("selection")));
    }
    
    public function e_show_document(tauAjaxEvent $e)
    {                   
        $viewables = $this->document->prepareForViewer();
        
        $this->setData('');
        
        //add the highlight selector
        $this->addChild($this->highlighter = new tauAjaxXmlTag('div'));
        $this->highlighter->setAttribute("id", "highlighter");  
        
        //show the document content
        foreach($viewables as $viewable)
        {
            $this->addChild($viewable);
            $viewable->initDisaggregator();
        }
        
        //javascript for the highlight selector
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
                _client.sendEvent("update", _target, {"selection": selection});                
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

class Loader extends tauAjaxXmlTag
{
    public function __construct()
    {
        parent::__construct('div');
        
        $this->addClass("hexdots-loader");
    }
}

?>
