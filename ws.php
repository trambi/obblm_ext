<?php
//header('Content-Type: application/json'); 
//header('Access-Control-Allow-Origin: *');

require_once 'header.php';
require_once 'settings.php';
require_once 'lib/class_module.php';
require_once 'lib/mysql.php';
require_once 'lib/class_tournament.php';
require_once 'modules/extendedrankings/class_extendedrankings.php';
require_once 'lib/misc_functions.php';
require_once 'lib/class_match.php';
require_once 'lib/class_stats.php';
require_once 'lib/class_player.php';
require_once 'lib/class_team.php';

function json_test($type){
	return array('action' => 'test');  	
}


if( true === isset($_GET['action']) ){
	$action = $_GET['action'];
    echo 'action : ', $action,'<br />';
	mysql_up(false);
	$returnObject = array();
	if( 'test' === $action ){
		$returnObject = json_test();	
	}
	if( 'getRanking' === $action ){
		$mod = new ExtendedRankings();
		if( true === isset($_GET['trid']) ){
			$trid = intval($_GET['trid']);
			$mod->setRankingsForATournament($trid);
			$returnObject = $mod->tournaments[0];			
		}
	}
	if('getTournaments' === $action){
		$returnObject = Tour::getBegunAndNotFinishedTours();
	}
	if( 'getMatches' === $action ){
		if( true === isset($_GET['trid']) ){
			$trid = intval($_GET['trid']);
			$returnObject = Match::getMatches(array(), STATS_TOUR, $trid,false);			
		}
	}
	if( 'getTeam' === $action ){
		if( true === isset($_GET['teamId']) ){
			$teamId = intval($_GET['teamId']);
			$team = new Team($teamId);
            $team->getPlayers();
            $returnObject = $team;
		}
	}
}else{
	$returnObject = array(
		'getRanking'=>array(
			'help'=>'Get the (extended) ranking of the tournament with id trid',
			'parameters'=>array(
				'action'=>'getRanking',
				'trid'=>'id of the tournament'),
			'example'=>'ws.php?action=getRanking&trid=40'
		),
		'getMatches'=>array(
			'help'=>'Get all games of the tournament with id trid',
			'parameters'=>array(
				'action'=>'getMatches',
				'trid'=>'id of the tournament'),
			'example'=>'ws.php?action=getMatches&trid=40'
		),
		'getTournaments'=>array(
			'help'=>'Get all tournaments opened and not finished',
			'parameters'=>array(
				'action'=>'getTournaments'),
			'example'=>'ws.php?action=getTournaments'
		),
		'getTeam'=>array(
			'help'=>'Get team by id',
			'parameters'=>array(
				'action'=>'getTeam',
				'teamId'=>'id of the team'),
			'example'=>'ws.php?action=getTeam&teamId=255'
		),
	);  
}

echo json_encode($returnObject);
?>