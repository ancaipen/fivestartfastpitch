<?php
defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;

$document = Factory::getDocument();

//$document = JFactory::getDocument();

$document->addCustomTag( '<script type="text/javascript" src="templates/OhioBaseball/scripts/lightbox/js/prototype.js"></script>' );
$document->addCustomTag( '<script type="text/javascript" src="templates/OhioBaseball/scripts/lightbox/js/scriptaculous.js?load=effects,builder"></script>' );
$document->addCustomTag( '<script type="text/javascript" src="templates/OhioBaseball/scripts/lightbox/js/lightbox.js"></script>' );
$document->addCustomTag( '<link rel="stylesheet" href="templates/OhioBaseball/scripts/lightbox/css/lightbox.css" type="text/css" media="screen" />' );

?>
<?php

    $html = $this->results;
    echo $html;
?>