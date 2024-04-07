<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/*
$user = & JFactory::getUser();
   
if (!$user->authorize( 'com_ts', 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}
 *
 * error_reporting(E_ALL);
ini_set('display_errors', '1');
 */

$view = "tournament";
if(isset($_GET['view']))
{
  $view = $_GET['view'];
}

//tournaments
// if ($view == 'tournament')
// {
//     require_once (JPATH_COMPONENT.DS.'controllers'.DS.'tournament.controller.php');
//     $controller = new TSAdminControllerTournament();
//     $controller->execute(Factory::getApplication()->input->getCmd('task'));
//     $controller->redirect();
// }

if($view=='tournament')
{
    require_once (JPATH_COMPONENT.DS.'controllers'.DS.'tournament.controller.php');
    $controller = new TSAdminControllerTournament();
    $input = Factory::getApplication()->input;
    $task = $input->getCmd('task');
    $controller->execute($task);

    //$controller->execute(JRequest::getVar('task'));
    //$controller->redirect();
}


else if($view=='tournament_cost')
{
    // require_once (JPATH_COMPONENT.DS.'controllers'.DS.'tournament.controller.php');
    // $controller = new TSAdminControllerTournament();
    // $controller->execute(Factory::getApplication()->input->getCmd('task'));
    // $controller->redirect();
    require_once (JPATH_COMPONENT.DS.'controllers'.DS.'tournament_cost.controller.php');
    $controller = new tsAdminControllertournament_cost();
    $input = Factory::getApplication()->input;
    $task = $input->getCmd('task');
    $controller->execute($task);
    // $controller->execute(JRequest::getVar('task'));
    // $controller->redirect();
}
else if($view=='registration')
{
    // require_once (JPATH_COMPONENT.DS.'controllers'.DS.'tournament.controller.php');
    // $controller = new TSAdminControllerTournament();
    // $controller->execute(Factory::getApplication()->input->getCmd('task'));
    // $controller->redirect();
    require_once (JPATH_COMPONENT.DS.'controllers'.DS.'registration.controller.php');
    $controller = new TSAdminControllerRegistration();
    $input = Factory::getApplication()->input;
    $task = $input->getCmd('task');
    $controller->execute($task);
    // $controller->execute(JRequest::getVar('task'));
    // $controller->redirect();
}
//games
else if($view=='game')
{
    // require_once (JPATH_COMPONENT.DS.'controllers'.DS.'tournament.controller.php');
    // $controller = new TSAdminControllerTournament();
    // $controller->execute(Factory::getApplication()->input->getCmd('task'));
    // $controller->redirect();
    require_once (JPATH_COMPONENT.DS.'controllers'.DS.'game.controller.php');
    $controller = new TSAdminControllerGame();
    $input = Factory::getApplication()->input;
    $task = $input->getCmd('task');
    $controller->execute($task);
    // $controller->execute(JRequest::getVar('task'));
    // $controller->redirect();
}

?>
