<?php

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseModel;

jimport('joomla.application.component.controller');

class tsAdminControllertournament_cost extends JControllerLegacy
{

    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit', 'save', 'remove');
    }

    function getViewModel($name)
    {

        $document = Factory::getDocument();
        $input = Factory::getApplication()->input;
        $viewName = $input->get('view', $name, 'cmd');
        //$viewName = JRequest::getVar('view', $name);
        $viewType= $document->getType();

        //get our view
        $view = $this->getView($viewName, $viewType, 'tsAdminView');

        //get the model
        $model = $this->getModel($viewName, 'tsAdminModel');

        //some error chec
        if (!$model instanceof BaseModel)
        {
            // Handle the error condition
        }
        else
        {
            $view->setModel($model, true);
        }
        // if (!JError::isError($model))
        // {
        //  $view->setModel ($model, true);
        // }

        $view->setLayout('default');
        $view->display();

        return $view;

    }

    function display ($cachable = false, $urlparams = false)
    {
        //get the view name from the query string
        $view=tsAdminControllertournament_cost::getViewModel('tournament_cost');
    }

    //controller save call
    function save()
    {

        $view=tsAdminControllertournament_cost::getViewModel('tournament_cost');
        $input = Factory::getApplication()->input;
        $cid = $input->get('cid', [], 'array');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
       
        $post = $input->getArray($_POST);
        //$post = JRequest::get('post');
        $error_msg = "";

        $error_msg = tsAdminModeltournament_cost::save_tournament_cost($cid,$post);

        if($error_msg == '')
        {
            //redirect back to landing page
            //JApplication::redirect('index.php?option=com_ts&view=tournament_cost');
                $app = JFactory::getApplication();
                $app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=tournament_cost'));
        }
        else
        {
            //set error session
            $_SESSION['error_msg'] = $error_msg;
        }

        return $error_msg;

    }

    function add()
    {
        $view=tsAdminControllertournament_cost::getViewModel('tournament_cost');
    }

    function edit()
    {
        $view=tsAdminControllertournament_cost::getViewModel('tournament_cost');
    }

    function remove()
    {

        $view=tsAdminControllertournament_cost::getViewModel('tournament_cost');
        $input = Factory::getApplication()->input;
        $cid = $input->get('cid', [], 'ARRAY');
        $post = $input->getArray($_POST);
        // $cid = JRequest::getVar('cid', array(), 'request', 'array');
        // $post = JRequest::get('post');

        if(isset($post["cid"]))
        {
            foreach($post["cid"] as $p)
            {
                tsAdminModeltournament_cost::delete_tournament_cost($p);
            }
            //JApplication::redirect('index.php?option=com_ts&view=tournament_cost');
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=tournament_cost'));
         }

    }

    function cancel()
    {
        $view=tsAdminControllertournament_cost::getViewModel('tournament_cost');
        //JApplication::redirect('index.php?option=com_ts&view=tournament_cost');
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=tournament_cost'));

    }

    function apply()
    {

        $view=tsAdminControllertournament_cost::getViewModel('tournament_cost');
        $curr_url = "https://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
        $input = Factory::getApplication()->input;
        $cid = $input->get('cid', [], 'array');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = $input->getArray($_POST);
        //$post = JRequest::get('post');
        $error_msg = "";

        $error_msg = tsAdminModeltournament_cost::save_tournament_cost($cid,$post);

        if($error_msg == '')
        {
            //JApplication::redirect($curr_url);
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_($curr_url));
        }
        else
        {
            //set error session
            $_SESSION['error_msg'] = $error_msg;
        }

        return $error_msg;
    }

}

?>
