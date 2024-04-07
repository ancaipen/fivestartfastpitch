<?php

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Request\Server;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Model\AdminModel;


jimport('joomla.application.component.controller');

class TSAdminControllerTournament extends JControllerLegacy
{

    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit', 'save', 'remove');
    }

    function getViewModel($name)
    {

        $document= JFactory::getDocument();

        $request = Factory::getApplication()->input;
        $viewName = $request->getString('view', $name);
        //$viewName = JRequest::getVar('view', $name);
        $viewType= $document->getType();

        //get our view
        $view = $this->getView($viewName, $viewType);

        //get the model
        $model = $this->getModel($viewName, 'TSAdminModel');

        //some error chec
        //if (!JError::isError($model))
        if (!$model->getError())
        {
         $view->setModel ($model, true);
        }

        $view->setLayout('default');
        $view->display();

        return $view;

    }

    function display ($cachable = false, $urlparams = false)
    {
        //get the view name from the query string
        $view=TSAdminControllerTournament::getViewModel('tournament');
    }

    function save()
    {
        
        $view=TSAdminControllerTournament::getViewModel('tournament');

        $cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = Factory::getApplication()->input->post->getArray();
        //$post = JRequest::get('post');
        $message = '';
        
        if(isset($cid[0]))
        {
            if(trim($cid[0]) != "")
            {
                //update tournament
                $message = TSAdminModelTournament::update_tournament($post);
                if($message=='')
                {
                    //JApplication::redirect('index.php?option=com_ts');
					$app = JFactory::getApplication();
					$app->redirect(JRoute::_('/administrator/index.php?option=com_ts'));
                }
                else
                {
                    //set error message session
                    $_SESSION['ts_message'] = $message;
                    echo $message;
                }
            }
            else
            {
                //insert tournament
                $message = TSAdminModelTournament::insert_tournament($post);
                if($message=='')
                {
                    //JApplication::redirect('index.php?option=com_ts');
					$app = JFactory::getApplication();
					$app->redirect(JRoute::_('/administrator/index.php?option=com_ts'));
                }
                else
                {
                    //set error message session
                    $_SESSION['ts_message'] = $message;
                    echo $message;
                }
            }
        }
        else
        {
            //pass in post varaibles and insert records
            $message = TSAdminModelTournament::insert_tournament($post);
            if($message=='')
            {
                //JApplication::redirect('index.php?option=com_ts');
				$app = JFactory::getApplication();
				$app->redirect(JRoute::_('/administrator/index.php?option=com_ts'));
            }
            else
            {
                //set error message session
                $_SESSION['ts_message'] = $message;
                echo $message;
            }
        }
        
        

    }

    function add()
    {
        $view=TSAdminControllerTournament::getViewModel('tournament');
    }

    function edit()
    {
        $view=TSAdminControllerTournament::getViewModel('tournament');
    }

    function remove()
    {   

        $view=TSAdminControllerTournament::getViewModel('tournament');
        $cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = Factory::getApplication()->input->post->getArray();
        //$post = JRequest::get('post');


        if(isset($post["cid"]))
        {
            foreach($post["cid"] as $p)
            {
                TSAdminModelTournament::delete_tournament($p);
            }
            //JApplication::redirect('index.php?option=com_ts');
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('/administrator/index.php?option=com_ts'));
         }
        
    }

    function cancel()
    {
        $view=TSAdminControllerTournament::getViewModel('tournament');
        //JApplication::redirect('index.php?option=com_ts');
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('/administrator/index.php?option=com_ts'));
    }

    function apply()
    {
        
        $view=TSAdminControllerTournament::getViewModel('tournament');

        $cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = Factory::getApplication()->input->post->getArray();
        //$post = JRequest::get('post');

        if(isset($cid[0]))
        {
            //add update code here
            if(trim($cid[0]) != "")
            {
                //update tournament
                TSAdminModelTournament::update_tournament($post);
                //JApplication::redirect('index.php?option=com_ts&cid[]='.$cid[0]);
				$app = Factory::getApplication();
				$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&cid[]='.$cid[0], false));
            }
            else
            {
                //insert tournament
                $message = TSAdminModelTournament::insert_tournament($post);
                if($message!='')
                {
                    //set error message session
                    $_SESSION['ts_message'] = $message;
                    echo $message;
                }
            }
        }
        else
        {
            //pass in post varaibles and insert records
            $message = TSAdminModelTournament::insert_tournament($post);
            if($message!='')
            {
                //set error message session
                $_SESSION['ts_message'] = $message;
                echo $message;
                //JApplication::redirect('index.php?option=com_ts');
				$app = JFactory::getApplication();
				$app->redirect(JRoute::_('/administrator/index.php?option=com_ts'));
            }
        }      

    }

}

?>