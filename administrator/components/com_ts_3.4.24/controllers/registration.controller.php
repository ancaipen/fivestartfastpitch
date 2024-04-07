<?php

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\Error\Exception\ModelException;
jimport('joomla.application.component.controller');
jimport('phpexcel.library.PHPExcel');

class TSAdminControllerRegistration extends JControllerLegacy
{

    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit', 'apply', 'save', 'remove', 'export');
    }

    function getViewModel($name)
    {

        $document = Factory::getDocument();
		$input = Factory::getApplication()->input;
		$viewName = $input->get('view', $name, 'cmd');
        //$viewName = JRequest::getVar('view', $name);
        $viewType = $document->getType();

        //get our view
        $view = $this->getView($viewName, $viewType);

        //get the model
        $model = $this->getModel($viewName, 'TSAdminModel');

        //some error chec
		try {
			if (!($model instanceof BaseModel)) {
				throw new ModelException('Model is not an instance of BaseModel');
			}
			
			$view->setModel($model, true);
		} catch (ModelException $e) {
			// Handle the exception
		}
        // if (!JError::isError($model))
        // {
		// 	$view->setModel ($model, true);
        // }

        $view->setLayout('default');
        $view->display();

        return $view;

    }

    function display ($cachable = false, $urlparams = false)
    {
        //get the view name from the query string
        $view=TSAdminControllerRegistration::getViewModel('registration');
    }

    function remove()
    {
        
        $view=TSAdminControllerRegistration::getViewModel('registration');

		$cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
		$post = Factory::getApplication()->input->post->getArray();
        //$post = JRequest::get('post');

        //delete each selection
        if(isset($cid))
        {
            foreach ($cid as &$regstration_id) {
                TSAdminModelRegistration::DeleteRegistration($regstration_id);
            }
        }
		
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=registration'));

    }

	function export()
    {
		
        $view=TSAdminControllerRegistration::getViewModel('registration');
		
		$cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
        //$cid = JRequest::getVar('cid', array(), 'request', 'array');
		$post = Factory::getApplication()->input->post->getArray();
        //$post = JRequest::get('post');
        
		$query_where = '';
		
		if(isset($cid))
        {
            foreach ($cid as $regstration_id) 
			{
				$query_where .= $regstration_id.',';
            }
        }
		
		if(trim($query_where) != '')
		{
			$query_where = substr($query_where, 0, (strlen($query_where) - 1)); 
			$query_where = 'AND registration_id in ('.$query_where.')';
		}
				
		
		$objPHPExcel = new PHPExcel();

		// Set the active Excel worksheet to sheet 0
		$objPHPExcel->setActiveSheetIndex(0); 
		// Initialise the Excel row number
		$rowCount = 1; 
		
		//Set the active Excel worksheet to sheet 0 
		$query = "select distinct 
		t.tournament_name, 
		s.season_name, 
		reg.team_name, 
		reg.date_submitted, 
		IFNULL(reg.reg_status, 'New') as reg_status, 
		reg.team_manager_1, 
		reg.team_manager_2,
		reg.cell_phone_1,
		reg.cell_phone_2,		
		reg.email_1,
		reg.email_2,		
		reg.comments, 
		reg.registration_id,
		rt.waitlist 		
		from jos_ts_register reg 
		inner join  jos_ts_register_tourn rt on rt.register_id=reg.registration_id 
        inner join jos_ts_age a on a.age_id=rt.age_id
        inner join jos_ts_tournament t on t.tournament_id=rt.tournament_id
        inner join jos_ts_tournament_age_cost ac on ac.age_id=rt.age_id AND ac.tournament_id=rt.tournament_id
        INNER JOIN jos_ts_season s on s.season_id=t.season_id
        WHERE (t.is_deleted = 0 OR t.is_deleted IS NULL) 
        AND s.season_current = 1 
		AND reg.reg_status <> 'Deleted' ";
		
		$query .= $query_where;
		
		//echo $query;
		
		$db = Factory::getDBO();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		
		//add title row
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'tournament_name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'season_name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, 'team_name'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, 'reg_status'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, 'team_manager_1');
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, 'team_manager_2'); 		
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, 'cell_phone_1'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, 'cell_phone_2'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, 'email_1');
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, 'email_2'); 		
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, 'comments'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, 'waitlist'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, 'registration_id'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, 'date_submitted'); 
		$rowCount++; 
		
		foreach($rows as $row)
		{ 
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row->tournament_name);	 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row->season_name); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row->team_name); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row->reg_status); 
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row->team_manager_1);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row->team_manager_2);			
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row->cell_phone_1); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row->cell_phone_2);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row->email_1); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row->email_2);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row->comments);
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row->waitlist);			
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row->registration_id);
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row->date_submitted); 
			$rowCount++;	
		} 

		// Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
		
		$export_file = '/home/ohiobas1/public_html/ohiobaseball/administrator/components/com_ts/exports/registration_export.xlsx';
		
		// Write the Excel file to filename some_excel_file.xlsx in the current directory
		$objWriter->save($export_file); 
		
		header('Content-Description: File Transfer');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=\"".basename($export_file)."\"");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Length: ' . filesize($export_file)); //Remove

		ob_clean();
		flush();
		readfile($export_file);
		exit();
		
        //redirect back to landing page
        //JApplication::redirect('index.php?option=com_ts&view=registration');
    }
	
	function apply()
    {
        $view=TSAdminControllerRegistration::getViewModel('registration');
		$input = Factory::getApplication()->input;
		$cid = $input->get('cid', [], 'array');
		//$cid = JRequest::getVar('cid', array(), 'request', 'array');
		$post = $input->post->getArray();
        //$post = JRequest::get('post');
		
        //update each selection
        if(isset($cid))
        {
            foreach ($cid as $regstration_id) {
				$reg_status = $_POST['reg_status_'.$regstration_id];
                TSAdminModelRegistration::UpdateRegistrationStatus($regstration_id, $reg_status);
            }
        }
		
        //redirect back to landing page
        //JApplication::redirect('index.php?option=com_ts&view=registration');
		
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('/administrator/index.php?option=com_ts&view=registration'));
		
    }
	
    function cancel()
    {
        $view=TSAdminControllerRegistration::getViewModel('registration');
        //redirect back to landing page
        JApplication::redirect('index.php?option=com_ts&view=registration');
    }

}

?>