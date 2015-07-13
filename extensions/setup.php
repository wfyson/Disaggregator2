<?php

$conf = iotaConf::getInstance();
$conf->addKey('document_Dir', new pathToIotaStoreFSDriver(), 'Where the documents are stored.'); 

//error_log($conf->document_dir);

$store = iotaStore::getInstance();
$store->mount($conf->document_dir, '/site/documents', true);

$loader = iotaLoader::getInstance();

$loader->load(array(
 
        'bootstrap/button.class.php', 
        'bootstrap/badge.class.php', 
	'bootstrap/jumbotron.class.php',   
        'bootstrap/modal.class.php',
    
        'documents/documentBrowser.class.php',
	
	'components/components.class.php',
        'components/descriptorCreator.class.php'

	), dirname(__FILE__) . '/ajax/');


?>
