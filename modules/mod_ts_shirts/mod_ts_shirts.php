<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

$tournament_id = "";
$html = "";

//get tournament_id if available
if(isset($_GET['tournament_id']))
{
    $tournament_id = $_GET['tournament_id'];
}

if(isset($tournament_id))
{
    if($tournament_id != "")
    {
        $html = mod_ts_shirts::get_shirt_html($tournament_id);
    }
}

//loads forms from tmpl\default.php
require( JModuleHelper::getLayoutPath( 'mod_ts_shirts' ) );

?>
