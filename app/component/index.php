<?php

$model = DisaggregatorModel::get();

if(isset($_GET['component']))
{
    $component = $model->component->getRecordByPK($_GET['component']) ? $model->component->getRecordByPK($_GET['component']) : null;
}

$componentPage = new ComponentPage($component);

tauAjaxServerHandler::initElement($componentPage, 'disaggregator.ajax.php');	



?>
