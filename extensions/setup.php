<?php

$conf = iotaConf::getInstance();
$conf->addKey('document_Dir', new pathToIotaStoreFSDriver(), 'Where the documents are stored.'); 
$conf->addKey('file_Dir', new pathToIotaStoreFSDriver(), 'Where the files are stored.'); 

$store = iotaStore::getInstance();
$store->mount($conf->document_dir, '/site/documents', true);
$store->mount($conf->file_dir, '/site/files', true);

$loader = iotaLoader::getInstance();

$loader->load(array(
 
    'bootstrap/alert.class.php',
    'bootstrap/badge.class.php',
    'bootstrap/button.class.php',
    'bootstrap/collapsible.class.php',
    'bootstrap/glyphicon.class.php',
    'bootstrap/header.class.php', 
    'bootstrap/input.class.php', 
    'bootstrap/jumbotron.class.php',
    'bootstrap/label.class.php',
    'bootstrap/listGroup.class.php',
    'bootstrap/modal.class.php',
    'bootstrap/panel.class.php',
    'bootstrap/progress.class.php',
    'bootstrap/select.class.php',
    'bootstrap/table.class.php',
    'bootstrap/tabs.class.php',
    
    'documents/documentBrowser.class.php',
    'documents/document.class.php',
	
    'components/componentBrowser.class.php',
    'components/components.class.php',
    'components/descriptorBrowser.class.php',
    'components/descriptorCreator.class.php',
    'components/fieldCreator.class.php',
    'components/componentPage.class.php',
    'components/namespace.class.php',
    
    'disaggregator/disaggregator.class.php',
    'disaggregator/documentSelector.class.php',
    'disaggregator/descriptorSelector.class.php',
    'disaggregator/componentBuilder.class.php',
    'disaggregator/documentViewer.class.php',
    'disaggregator/viewable.class.php',
    'disaggregator/stages/builderStage.class.php',
    'disaggregator/stages/textStage.class.php',
    'disaggregator/stages/fileStage.class.php',
    'disaggregator/stages/componentStage.class.php',
    'disaggregator/stages/contributorStage.class.php',
    
    'redactor/redactor.class.php',
    'redactor/redactorViewer.class.php',
    'redactor/redactorControls.class.php',
    
    'contributors/contributorBrowser.class.php',
    'contributors/contributorAdder.class.php',
    
    'scanners/scanner.class.php',
    'scanners/scannerList.class.php',
    
    'overview/overview.class.php',
    
    'register/register.class.php',
    
    'profile/profile.class.php',   
    
    'portfolio/portfolio.class.php',
    'portfolio/documentPortfolio.class.php',
    'portfolio/contributionPortfolio.class.php',


), dirname(__FILE__) . '/ajax/');

$loader->load(array(
    'readers/openXMLReader.class.php',
    'readers/wordReader.class.php',
    'readers/powerpointReader.class.php',
    'readers/pdfReader.class.php',
    
    'scanners/scanner.interface.php',
    'scanners/oscar.class.php',
    'scanners/chemtagger.class.php',
    
    'orcid/curl.class.php',
    'orcid/orcid.class.php',
    
    'writers/wordWriter.class.php',
    
    'linkeddata/linkedDataHelper.class.php'
), dirname(__FILE__));


?>
