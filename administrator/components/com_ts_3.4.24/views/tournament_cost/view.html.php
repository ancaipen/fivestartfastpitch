<?php

defined('_JEXEC') or die ('restrinced access');
jimport('joomla.application.component.view');

class tsAdminViewtournament_cost extends JViewLegacy
{

   function display ($tpl=null)
   {

      global $option,$mainframe;
      $model = $this->getModel();
      parent::display($tpl);

   }

}

?>