<?php
require_once('class_extendedrankings_cup_strategy.php');

function test_compare_team_cup_elt($points1,$points2,$attendedResult,$extraComments){
	$bReturn = false;
	$team1 = new stdClass();
	$team1->points = $points1;
	$team1->id = 1;
	$team2 = new stdClass();
	$team2->points = $points2;
	$team2->id = 2;
	$strategy = new ExtendedRankingsCupStrategy();
	$result = $strategy->compareTeam($team1, $team2);
	
	if($attendedResult != $result){
		echo 'compare_team_championship avec ';
		echo 'team 1 (points : ',$points1,') vs ';
		echo 'team 2 (points : ',$points2,') ';
		if(strlen($extraComments)){
			echo $extraComments;
		}
		echo ' : KO (',$result,')<br />';
	}else{
		$bReturn = true;
	}
	return $bReturn;
}

function test_get_points_from_match_cup_elt($strategy,$round,$score,$opponentScore,$attendedPoints,$attendedOpponentPoints){
	$bReturn = false;
	$match = new stdClass();
	$match->score = $score;
	$match->opponentScore = $opponentScore;
	$match->round= $round;
	$reverseMatch = new stdClass();
	$reverseMatch->score = $opponentScore;
	$reverseMatch->opponentScore = $score;
	$reverseMatch->round= $round;
	$points = $strategy->getPointsFromMatch($match);
	$opponentPoints = $strategy->getPointsFromMatch($reverseMatch);
		
	if($attendedPoints != $points){
		echo 'get_points_from_match_cup round : ',$round,' with ';
		echo $score,' - ',$opponentScore,' : ';
		echo 'KO (',$points,')<br />';
	}elseif ($attendedOpponentPoints != $opponentPoints){
		echo 'get_points_from_match_cup revert round : ',$round,' with ';
		echo $opponentScore,' - ',$score,' : ';
		echo 'KO (',$opponentPoints,')<br />';
	}else{
		$bReturn = true;
	}
	return $bReturn;
}

function test_compare_team_cup(){
	
}

function test_get_points_from_match_cup(){
	//socks winter cup 2012
	$strategy = new ExtendedRankingsCupStrategy();
	// Premier tour 
	test_get_points_from_match_cup_elt($strategy,1,1,3,0,2);
	test_get_points_from_match_cup_elt($strategy,1,0,2,0,2);
	test_get_points_from_match_cup_elt($strategy,1,6,0,2,0);
	test_get_points_from_match_cup_elt($strategy,1,0,2,0,2);
	// quart de finale
	test_get_points_from_match_cup_elt($strategy,252,0,1,0,1024);
	test_get_points_from_match_cup_elt($strategy,252,4,3,1024,0);
	test_get_points_from_match_cup_elt($strategy,252,2,1,1024,0);
	test_get_points_from_match_cup_elt($strategy,252,2,0,1024,0);
	// demi
	test_get_points_from_match_cup_elt($strategy,253,1,2,0,2048);
	test_get_points_from_match_cup_elt($strategy,253,1,2,0,2048);
	// final
	test_get_points_from_match_cup_elt($strategy,254,1,0,4096,0);
}




?>