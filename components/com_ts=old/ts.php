<?php

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once (JPATH_COMPONENT.DS.'controller.php');

$controller = new TSController();
$task = Factory::getApplication()->input->get('task');
//$controller->execute(JRequest::getVar('task'));
$controller->execute($task);
$controller->redirect();

?>
