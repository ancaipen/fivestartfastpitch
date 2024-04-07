<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
use Joomla\CMS\Helper\ModuleHelper;
// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

$html = mod_ts_tourn_summary::GetCurrentTournSummary();

require( ModuleHelper::getLayoutPath( 'mod_ts_tourn_summary' ) );

?>