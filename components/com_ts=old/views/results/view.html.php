<?php

defined('_JEXEC') or die ('restrinced access');
jimport('joomla.application.component.view');

class TSViewResults extends JViewLegacy
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

      $age_id = "";
      if(isset($_GET['age_id']))
      {
          $age_id=$_GET['age_id'];
      }

      //get html to display

      if ($tournament_id != '' && $age_id == '')
      {
      //print out ages here
        $html = $model->GetTournamentResults($tournament_id);

      }
      else if ($tournament_id != '' && $age_id != '')
     {
     //print out schedule/results here

        $html = $model->GetScheduleResults($tournament_id, $age_id);

     }

      

      //assign variable here to display on tmpl/default.php file
      //$this->assignRef('results', $html);
      $this->results =  $html;
      parent::display($tpl);
      
   }
   
}

?>
