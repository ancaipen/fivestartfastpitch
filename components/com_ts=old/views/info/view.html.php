<?php

defined('_JEXEC') or die ('restrinced access');
jimport('joomla.application.component.view');

class TSViewInfo extends JViewLegacy
{

   function display ($tpl=null)
   {

      global $option,$mainframe;
      $model = $this->getModel();

      //get game and tournament querystring values if they are available
      $tournament_id = "";
      if(isset($_GET['tournament_id']))
      {
          $tournament_id=$_GET['tournament_id'];
      }

      //get html to display
      $html = $model->GetScheduleInfo($tournament_id);

      //assign variable here to display on tmpl/default.php file
      $this->info = $html;

      parent::display($tpl);
      
   }
   
}

?>
