<?php

defined('_JEXEC') or die ('restrinced access');
jimport('joomla.application.component.view');

class TSAdminViewRegistration extends JViewLegacy
{
   public function display ($tpl=null)
   {
      global $option,$mainframe;
      $model = $this->getModel();
      parent::display($tpl);      
   }   
}

?>
