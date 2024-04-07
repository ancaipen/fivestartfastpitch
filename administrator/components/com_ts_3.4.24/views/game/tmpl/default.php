<style type="text/css">
    
.tourn_admin_container input, .tourn_admin_container textarea{
	padding: 3px;
}

tr.game_inactive td
{
    background-color: #FFCCCC;
}

</style>

<?php

defined ('_JEXEC') or die ('restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Editor\Editor;
//use Joomla\CMS\MVC\Model\BaseDatabaseModel;


//error_reporting (E_ALL ^ E_NOTICE);
//require_once JPATH_SITE . '/components/com_ts/models/game.php';

$document = Factory::getDocument();
$document->addScript('/includes/js/joomla.javascript.js');

$task = Factory::getApplication()->input->getCmd('task');
//$task = JRequest::getCmd('task');
$cid = Factory::getApplication()->input->get('cid', [], 'ARRAY');
//$cid = JRequest::getVar('cid', array(), 'request', 'array');
$post_base_url = "https://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];

$post = Factory::getApplication()->input->getArray($_POST);
//$post = JRequest::get('post');
//HTMLHelper::_('behavior.calendar');
//$rows = null;
$rows = [0];
$row_count = [0];

if($task=="" && !isset($cid[0]))
{
    
//filter settings
$f_tournament_id = "-1";
$f_age_id = "-1";

//check for sessions
if(isset($_SESSION['$f_tournament_id']))
{
    $f_tournament_id = $_SESSION['$f_tournament_id'];
}

if(isset($_SESSION['f_age_id']))
{
    $f_age_id = $_SESSION['f_age_id'];
}

//set filter values
if(isset($post['tournament_id']))
{
    $f_tournament_id = $post['tournament_id'];
    $_SESSION['$f_tournament_id'] = $post['tournament_id'];
}

if(isset($post['age_id']))
{
    $f_age_id = $post['age_id'];
    $_SESSION['f_age_id'] = $post['age_id'];
}

//gets all rows for admin section
$rows = TSAdminModelGame::getGameData($f_tournament_id,$f_age_id);

//set the toolbar here
TSAdminModelGame::setAllGameToolbar();

?>

<!-- VIEW MODE START -->

<form action="index.php?option=com_ts&view=game" method="post" id="adminForm" name="adminForm">
    <div class="game_filter" style="padding:10px 0 10px 0;clear:both;">
        <table cellpadding="3" cellspacing="0">
        <tr>
        <td>Tournament Name:</td><td><?php echo TSAdminModelGame::buildTournamentName($f_tournament_id, true);?></td>
        <td><?php echo TSAdminModelGame::buildTournamentAge($f_age_id, true);?></td>
        <td><input type="submit" value="filter" /></td>
        </tr>
        </table>
    </div>
        <table class="table table-striped">
                <thead>
                        <tr>
                                <th width="20">
								<?php echo HTMLHelper::_('grid.checkall'); ?>
								</th>
                                <th nowrap="nowrap" class="title">Season</th>
                                <th nowrap="nowrap">Tournament Name</th>
                                <th nowrap="nowrap">Age</th>
                                <th nowrap="nowrap">Game Date</th>
                                <th nowrap="nowrap">Game Time</th>
                                <th nowrap="nowrap">Game Type</th>
                                <th nowrap="nowrap">Team 1</th>
                                <th nowrap="nowrap">Score</th>
                                <th nowrap="nowrap">Team 2</th>
                                <th nowrap="nowrap">Score</th>

                        </tr>
                </thead>
                <tbody>
                        <?php

                        $k = 0;
                        for ($i=0, $n=count($rows); $i < $n; $i++) {

                                $row = &$rows[$i];

                                $checked = HTMLHelper::_('grid.id', $i, $row->game_id );
                                $link = 'index.php?option=com_ts&view=game&task=edit&cid[]='. $row->game_id;

                                //show inactive games
                                $tr_style = "";
                                $game_active = false;
                                $game_active = $row->game_active;

                                if($game_active == false)
                                {
                                    $tr_style = ' style="background-color:#FFCCCC;"';
                                }
                                else
                                {
                                    $tr_style = ' class="row'.$k.'"';
                                }

                                ?>

                                <tr<?php echo $tr_style; ?>>
                                        <td align="center"<?php echo $tr_style; ?>>
                                                <?php echo $checked; ?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->season_name; ?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->tournament_name; ?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->age; ?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <a href="<?php echo $link; ?>" title="Edit Game">
                                                        <?php echo  HTMLHelper::date($row->game_date); ?>
                                                </a>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->game_time_f; ?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->game_type;?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->home_team; ?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->home_score; ?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->visitor_team; ?>
                                        </td>
                                        <td<?php echo $tr_style; ?>>
                                                <?php echo $row->visitor_score; ?>
                                        </td>
                              
  
                                <?php

                                $k = 1 - $k;
                        }

                        ?>
                </tbody>
        </table>

        <input type="hidden" name="option" value="com_ts" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        
</form>
<!-- VIEW MODE END -->
<?php
}else{

//set the toolbar here
//$cid = [0];
//$rows = [0];
   if(!isset($cid[0])){
    $cid[0]=0;
   } 
$tourn_found = TSAdminModelGame::CheckActiveTournaments();
TSAdminModelGame::setGameToolbar($cid[0]);

$rows=TSAdminModelGame::getGameDataForm($cid[0]);

//display form if an active tournament is found
if($tourn_found == true)
{

//filter settings
$f_tournament_id = "-1";
$f_age_id = "-1";

//set defulats from session
if(isset($_SESSION['$f_tournament_id']))
{
    $f_tournament_id = $_SESSION['$f_tournament_id'];
}

if(isset($_SESSION['f_age_id']))
{
    $f_age_id = $_SESSION['f_age_id'];
}

//set defulats from db
if(isset($rows[0]->tournament_id))
{
    $f_tournament_id = $rows[0]->tournament_id;
}
if(isset($rows[0]->age_id))
{
    $f_age_id = $rows[0]->age_id;
}

// $_game_active = "";
// if($rows[0]->game_active==1)
// {
//     $_game_active = "CHECKED";
// }
if (!empty($rows) && property_exists($rows[0], 'game_active')) {
        // Check the value of game_active property
        $_game_active = $rows[0]->game_active == 1 ? "CHECKED" : "";
    } else {
        // Handle the case where $rows is empty or game_active property doesn't exist
        $_game_active = ""; // Or any default value you want to assign
    }

?>

<!-- EDIT MODE START -->
<form action="index.php?option=com_ts&view=game" method="post" name="adminForm" id="adminForm">
   <div class ="tourn_admin_container">
    <table cellpadding="3" cellspacing="0" border="0" width="70%">
        <tr><td class="hdr_title">Game Active:</td><td><input name="game_active" type="checkbox" <?php echo $_game_active; ?> /></td></tr>
        <tr><td>Tournament Name:</td><td><?php echo TSAdminModelGame::buildTournamentName($f_tournament_id);?></td></tr>
        <tr><td>Tournament Age:</td><td><?php echo TSAdminModelGame::buildTournamentAge($f_age_id);?></td></tr>
        <tr>
          <td>Game Date:</td><td><?php echo HTMLHelper::_('calendar', @$rows[0]->game_date, "game_date" , "game_date", '%Y-%m-%d');?>
          </td></tr>
        <tr>
         <td>Game Time:</td><td><input class="inputbox" type="text" name="game_time"
        id="game_time" size="25" maxlength="25"
        value="<?php echo @$rows[0]->game_time_f; ?>" /> <i>(ie 7:00 PM)</i></td></tr>
          <tr><td>Game Type:</td><td><?php echo TSAdminModelGame::buildGameType(@$rows[0]->game_type);?>
       </td></tr>
          <tr>
              <td>Game Order:</td>
              <td><input class="inputbox" type="text" name="game_order" id="game_order" size="5" maxlength="5" value="<?php echo @$rows[0]->game_order; ?>" /></td>
          </tr>
        <tr>
          <td>Field Location:</td><td><input class="inputbox" type="text" name="field_location"
        id="field_location" size="25" maxlength="25"
        value="<?php echo @$rows[0]->field_location; ?>" /></td></tr>
         <tr><td>Team 1:</td><td><input class="inputbox" type="text" name="home_team"
        id="home_team" size="100" maxlength="150"
        value="<?php echo @$rows[0]->home_team; ?>" /></td></tr>
         <tr><td>Team 1 Pool:</td><td><?php echo TSAdminModelGame::buildPool(@$rows[0]->home_pool, "home_pool");?></td></tr>
         <tr><td>Team 1 Score:</td><td><input class="inputbox" type="text" name="home_score"
        id="home_score" size="25" maxlength="25"
        value="<?php echo @$rows[0]->home_score; ?>" /></td></tr>
         <tr><td>Team 2:</td><td><input class="inputbox" type="text" name="visitor_team"
        id="visitor_team" size="100" maxlength="150"
        value="<?php echo @$rows[0]->visitor_team; ?>" /></td></tr>
         <tr><td>Team 2 Pool:</td><td><?php echo TSAdminModelGame::buildPool(@$rows[0]->visitor_pool,"visitor_pool");?></td></tr>
         <tr><td>Team 2 Score:</td><td><input class="inputbox" type="text" name="visitor_score"
        id="visitor_score" size="25" maxlength="25"
        value="<?php echo @$rows[0]->visitor_score; ?>" /></td></tr>
         <tr><td>Game Notes:</td><td>
        <?php $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
                //$editor = JFactory::getEditor();
                $params = array( 'smilies'=> '0' ,
                                 'style'  => '1' ,  
                                 'layer'  => '0' , 
                                 'table'  => '0' ,
                                 'clear_entities'=>'0'
                                 );
                echo $editor->display( 'notes', @$rows[0]->notes,'600', '400', '20', '20', false, null, null, null, $params );?></td></tr>
    </table>
   </div>
    
<input type="hidden" name="option" value="com_ts" />
<input type="hidden" name="view" value="game" />
<input type="hidden" name="game_id" value="<?php echo @$rows[0]->game_id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="cid[]" value="<?php echo @$rows[0]->game_id; ?>" />
<input type="hidden" name="controller" value="" />

</form>
<!-- EDIT MODE END -->
<?php } else { ?>
<h1>No active Tournaments found for this season.  Please <a href="index.php?option=com_ts">Click Here to add a new tournament</a></h1>
<?php } ?>

<?php } ?>