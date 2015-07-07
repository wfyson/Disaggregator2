<?php

$loader = iotaLoader::getInstance();
$loader->load(array('model.class.php'), dirname(__FILE__));
$loader->load(array('person.class.php'), dirname(__FILE__).'/model/');
$loader->loadAll(dirname(__FILE__).'/model/');

?>
