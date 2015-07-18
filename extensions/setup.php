<?php

$conf = iotaConf::getInstance();
$conf->addKey('document_Dir', new pathToIotaStoreFSDriver(), 'Where the documents are stored.'); 

$store = iotaStore::getInstance();
$store->mount($conf->document_dir, '/site/documents', true);

$loader = iotaLoader::getInstance();

$loader->load(array(
 
    'bootstrap/alert.class.php',
    'bootstrap/badge.class.php',
    'bootstrap/button.class.php', 
    'bootstrap/glyphicon.class.php',
    'bootstrap/header.class.php', 
    'bootstrap/jumbotron.class.php',
    'bootstrap/listGroup.class.php',
    'bootstrap/modal.class.php',
    
    'documents/documentBrowser.class.php',
	
    'components/components.class.php',
    'components/descriptorBrowser.class.php',
    'components/descriptorCreator.class.php',
    'components/fieldCreator.class.php',
    
    'disaggregator/disaggregator.class.php',
    'disaggregator/documentSelector.class.php',
    'disaggregator/descriptorSelector.class.php',
    'disaggregator/compoundBuilder.class.php',
    'disaggregator/documentViewer.class.php',
    'disaggregator/stages/builderStage.class.php',
    'disaggregator/stages/textStage.class.php',
    'disaggregator/stages/fileStage.class.php',
    'disaggregator/stages/compoundStage.class.php'

), dirname(__FILE__) . '/ajax/');


?>
