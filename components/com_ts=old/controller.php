<?php

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseModel;

jimport('joomla.application.component.controller');

class TSController extends JControllerLegacy
{

   function display ($cachable = false, $urlparams = false)
   {

      //get a refrence of the page instance in joomla
      $document= Factory::getDocument();
      //get the view name from the query string
      
      $query_view = 'results';
      if(isset($_GET['view']))
      {
          $query_view = $_GET['view'];
      }
      $input = Factory::getApplication()->input;
      $viewName = $input->get('view', $query_view);

      //$viewName = JRequest::getVar('view', $query_view);
      $viewType= $document->getType();
      
      //get our view
      $view = $this->getView($viewName, $viewType);
      //get the model
      $model = $this->getModel($viewName, 'ModelTS');

      //some error chec
      try {
         if (!($model instanceof BaseModel)) {
             throw new Exception('Model is not an instance of BaseModel');
         }
         
         $view->setModel($model, true);
         } catch (Exception $e) {
         // Handle the exception
         }
      // if (!JError::isError($model))
      // {
      //    $view->setModel ($model, true);
      // }
      
      //set the template and display it
      $view->setLayout('default');
      $view->display();

   }

}

?>
