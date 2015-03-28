<?php
require_once 'settings.php';
require_once 'lib/class_module.php';
require_once 'lib/mysql.php';
require_once 'lib/class_tournament.php';
require_once 'modules/extendedrankings/class_extendedrankings.php';
require_once 'lib/misc_functions.php';
require_once 'lib/class_match.php';

function json_test($type){
	return array('action' => 'test');  	
}

header('Content-Type: application/json'); 
if( true === isset($_GET['action']) ){
	$action = $_GET['action'];
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
	if( 'getMatches' === $action ){
		if( true === isset($_GET['trid']) ){
			$trid = intval($_GET['trid']);
			$returnObject = Match::getMatches(array(), STATS_TOUR, $trid,false);			
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
	);  
}

echo json_encode($returnObject);
?>
