<?php

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseModel;

jimport('joomla.application.component.controller');

class TSAdminControllerGame extends JControllerLegacy
{

    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit', 'save', 'remove', 'publish', 'unpublish');
    }

    function getViewModel($name)
    {

        $document = JFactory::getDocument();
        $input = Factory::getApplication()->input;
        $viewName = $input->get('view', $name, 'cmd');
        //$viewName = JRequest::getVar('view', $name);
        $viewType= $document->getType();

        //get our view
        $view = $this->getView($viewName, $viewType);

        //get the model
        $model = $this->getModel($viewName, 'TSAdminModel');

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
        $view=TSAdminControllerGame::getViewModel('game');
    }

    function save()
    {

        $view=TSAdminControllerGame::getViewModel('game');

        $cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
       // echo "<pre>";print_r($cid);echo "</pre>";
        // $cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = Factory::getApplication()->input->getArray($_POST);
       //echo "<pre>";print_r($post);echo "</pre>";exit;
        // $post = JRequest::get('post');

        $_game_active = 0;
        if($post['game_active'] == "on")
        {
            $_game_active = 1;
        }

        //find ids
        $_cid = "";
        $_game_id = "";

        if(isset($cid[0]))
        {
            $_cid = $cid[0];
        }

        if(isset($post['game_id']))
        {
            $_game_id = $post['game_id'];
        }

        if(trim($_cid) != "" && trim($_game_id) != "")
        {

            //update mode
            TSAdminModelGame::UpdateGame($post['game_id'],$_game_active,$post['home_team'],$post['visitor_team'],$post['home_pool'],$post['game_date'],$post['game_type'],
            $post['field_location'],$post['home_score'],$post['visitor_score'],$post['notes'],$post['age_id'],$post['tournament_id'],$post['visitor_pool'],$post['game_time'],$post['game_order']);
            
            //redirect back to landing page
            //JApplication::redirect('index.php?option=com_ts&view=game');
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=game'));

        }
        else
        {
            //insert mode
            if(isset($post))
            {
                $game_id = "";
                TSAdminModelGame::InsertGame($game_id,$_game_active,$post['home_team'],$post['visitor_team'],$post['home_pool'],$post['game_date'],$post['game_type'],
                $post['field_location'],$post['home_score'],$post['visitor_score'],$post['notes'],$post['age_id'],$post['tournament_id'],$post['visitor_pool'],$post['game_time'],$post['game_order']);
                
                //redirect to update page
                //JApplication::redirect('index.php?option=com_ts&view=game');
				$app = JFactory::getApplication();
				$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=game'));


            }
            
        }

    }

    function add()
    {
        $view=TSAdminControllerGame::getViewModel('game');
    }

    function edit()
    {
        $view=TSAdminControllerGame::getViewModel('game');
    }
    
    function publish()
    {

        $view=TSAdminControllerGame::getViewModel('game');

        $input = Factory::getApplication()->input;
        $cid = $input->get('cid', [], 'ARRAY');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = $input->post->getArray();
        //$post = JRequest::get('post');

        //publish each selection
        if(isset($cid))
        {
            foreach ($cid as &$game_id) {
                TSAdminModelGame::PublishGame($game_id);
            }
        }

        //refresh page
        //JApplication::redirect('index.php?option=com_ts&view=game');
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=game'));
        
    }
	
	function unpublish()
    {

        $view=TSAdminControllerGame::getViewModel('game');

        $input = Factory::getApplication()->input;
        $cid = $input->get('cid', [], 'ARRAY');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = $input->post->getArray();
        //$post = JRequest::get('post');

        //unpublish each selection
        if(isset($cid))
        {
            foreach ($cid as &$game_id) {
                TSAdminModelGame::PublishGame($game_id, "0");
            }
        }

        //refresh page
        //JApplication::redirect('index.php?option=com_ts&view=game');
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=game'));
        
    }
    
    function remove()
    {

        $view=TSAdminControllerGame::getViewModel('game');

        $input = Factory::getApplication()->input;
        $cid = $input->get('cid', [], 'ARRAY');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = $input->post->getArray();
        //$post = JRequest::get('post');
        

        //delete each selection
        if(isset($cid))
        {
            foreach ($cid as &$game_id) {
                TSAdminModelGame::DeleteGame($game_id);
            }
        }

        //refresh page
        //JApplication::redirect('index.php?option=com_ts&view=game');
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=game'));
        
    }

    function cancel()
    {
        $view=TSAdminControllerGame::getViewModel('game');
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=game'));
        //JApplication::redirect('index.php?option=com_ts&view=game');
    }

    function apply()
    {
        $view=TSAdminControllerGame::getViewModel('game');

        $cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
        // $cid = JRequest::getVar('cid', array(), 'request', 'array');
        $post = Factory::getApplication()->input->post->getArray();
        // $post = JRequest::get('post');
        

        $_game_active = 0;
        if($post['game_active'] == "on")
        {
            $_game_active = 1;
        }

        //find ids
        $_cid = "";
        $_game_id = "";

        if(isset($cid[0]))
        {
            $_cid = $cid[0];
        }

        if(isset($post['game_id']))
        {
            $_game_id = $post['game_id'];
        }

        $post = Factory::getApplication()->input->post->getArray();
        //$post['notes'] = JRequest::getVar( 'notes', '', 'post', 'string', JREQUEST_ALLOWHTML );
        $post['notes'] = preg_replace("/'/", "\&#39;", $post['notes']);
        
        if(trim($_cid) != "" && trim($_game_id) != "")
        {
            //update mode
            TSAdminModelGame::UpdateGame($post['game_id'],$_game_active,$post['home_team'],$post['visitor_team'],$post['home_pool'],$post['game_date'],$post['game_type'],
            $post['field_location'],$post['home_score'],$post['visitor_score'],$post['notes'],$post['age_id'],$post['tournament_id'],$post['visitor_pool'],$post['game_time'],$post['game_order']);

            //redirect back to landing page
            //JApplication::redirect('index.php?option=com_ts&view=game&task=edit&cid[]='.$cid[0]);
			
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=game&task=edit&cid[]='.$cid[0]));
			

        }
        else
        {
            //insert mode
            if(isset($post))
            {
                
                $game_id = "";
                $rows = TSAdminModelGame::InsertGame($game_id,$_game_active,$post['home_team'],$post['visitor_team'],$post['home_pool'],$post['game_date'],$post['game_type'],
                $post['field_location'],$post['home_score'],$post['visitor_score'],$post['notes'],$post['age_id'],$post['tournament_id'],$post['visitor_pool'],$post['game_time'],$post['game_order']);

                //redirect back to landing page
                //JApplication::redirect('index.php?option=com_ts&view=game&task=edit&cid[]='.$rows[0]->game_id);
				
				$app = JFactory::getApplication();
				$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=game&task=edit&cid[]='.$rows[0]->game_id));
				

            }

        }


    }

}

?>