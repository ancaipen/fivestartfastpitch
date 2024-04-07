<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

defined('_JEXEC') or die ('restrinced access');
jimport('joomla.application.component.view');

class TSAdminViewGame extends JViewLegacy
{


   function display ($tpl=null)
   {

      global $option,$mainframe;
      $model = $this->getModel();
      parent::display($tpl);
      
   }
   
}

?>
