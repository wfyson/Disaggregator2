<?php

// Get an instance of the server handler 
$handler = tauAjaxServerHandler::getInstance();

$handler->enableDebugging(true);

// Ask it to fetch events from $_GET and hand them to the appropriate object (stored in the session)
$handler->despatchEvents();

// Send the buffered response to the client (by echoing it)
$handler->flushCommands();


?>
