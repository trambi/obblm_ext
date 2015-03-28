<?php
require_once('class_extendedrankings_championship_strategy.php');

function test_compare_team_championship_elt($points1,$tdNet1,$points2,$tdNet2,$matches,$attendedResult,$extraComments){
	$bReturn = false;
	$team1 = new stdClass();
	$team1->points = $points1;
	$team1->tdNet = $tdNet1;
	$team1->id = 1;
	$team1->matches = $matches;
	$team2 = new stdClass();
	$team2->points = $points2;
	$team2->tdNet = $tdNet2;
	$team2->id = 2;
	$strategy = new ExtendedRankingsChampionshipStrategy();
	$result = $strategy->compareTeam($team1, $team2);
	
	if($attendedResult != $result){
		echo 'compare_team_championship avec ';
		echo 'team 1 (points : ',$points1,', tdNet : ',$tdNet1,') vs ';
		echo 'team 2 (points : ',$points2,', tdNet : ',$tdNet2,') ';
		if(strlen($extraComments)){
			echo $extraComments;
		}
		echo ' : KO (',$result,')<br />';
	}else{
		$bReturn = true;
	}
	return $bReturn;
}

function test_get_points_from_match_championship_elt($score,$opponentScore,$attendedPoints){
	$bReturn = false;
	$match = new stdClass();
	$match->score = $score;
	$match->opponentScore = $opponentScore;
	$strategy = new ExtendedRankingsChampionshipStrategy();
	$points = $strategy->getPointsFromMatch($match);
	
	if($attendedPoints != $points){
		echo 'get_points_from_match_championship with ',$score,' - ',$opponentScore,' : ';
		echo 'KO (',$points,')<br />';
	}else{
		$bReturn = true;
	}
	return $bReturn;
}

function test_compare_team_championship(){
	test_compare_team_championship_elt(10,1,0,1,array(),-1,'');
	test_compare_team_championship_elt(0,1,10,1,array(),1,'');
	test_compare_team_championship_elt(10,3,10,1,array(),-1,'');
	test_compare_team_championship_elt(10,1,10,3,array(),1,'');
	test_compare_team_championship_elt(10,1,10,1,array(),0,'');
	
	$comments = 'with one not concerned match';
	$matches = array();
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[99] = $dummyMatch;
	
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,0,$comments);
	
	$comments = 'with one big loss against opponent and one not concerned match';
	$matches = array();
	$match = new stdClass();
	$match->opponentPoints = 5;
	$match->points = 0;
	$match->opponentScore = 2;
	$match->score = 0;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $match;
	$matches[99] = $dummyMatch;
	
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,1,$comments);
	
	$comments =  'with one small loss against opponent and one not concerned match';
	$matches = array();
	$match = new stdClass();
	$match->opponentPoints = 5;
	$match->points = 1;
	$match->opponentScore = 1;
	$match->score = 0;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $match;
	$matches[99] = $dummyMatch;
	
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,1,$comments);
	
	$comments = 'with one draw against opponent and one "out of concern" match';
	$matches = array();
	$match = new stdClass();
	$match->opponentPoints = 2;
	$match->points = 2;
	$match->opponentScore = 1;
	$match->score = 1;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $match;
	$matches[99] = $dummyMatch;
	
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,0,$comments);
	
	$comments =  'with one small win against opponent and one not concerned match';
	$matches = array();
	$match = new stdClass();
	$match->opponentPoints = 1;
	$match->points = 5;
	$match->opponentScore = 1;
	$match->score = 2;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $match;
	$matches[99] = $dummyMatch;
	
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,-1,$comments);
	
	$comment = 'with one big win against opponent and one not concerned match';
	$matches = array();
	$match = new stdClass();
	$match->opponentPoints = 0;
	$match->points = 5;
	$match->opponentScore = 1;
	$match->score = 3;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $match;
	$matches[99] = $dummyMatch;
	
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,-1,$comments);
	
	$comment = 'with one small loss and one big win against opponent and one not concerned match';
	$matches = array();
	$opponentMatches = array();
	$match = new stdClass();
	$match->opponentPoints = 5;
	$match->points = 1;
	$match->opponentScore = 1;
	$match->score = 0;
	$opponentMatches[] = $match;
	$match = new stdClass();
	$match->opponentPoints = 0;
	$match->points = 5;
	$match->opponentScore = 0;
	$match->score = 3;
	$opponentMatches[] = $match;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $opponentMatches;
	$matches[99] = $dummyMatch;
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,-1,$comments);
	
	$comments = 'with one big loss and one small win against opponent and one not concerned match';
	$matches = array();
	$opponentMatches = array();
	$match = new stdClass();
	$match->opponentPoints = 5;
	$match->points = 0;
	$match->opponentScore = 2;
	$match->score = 0;
	$opponentMatches[] = $match;
	$match = new stdClass();
	$match->opponentPoints = 1;
	$match->points = 5;
	$match->opponentScore = 2;
	$match->score = 3;
	$opponentMatches[] = $match;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $opponentMatches;
	$matches[99] = $dummyMatch;
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,1,$comments);
	
	$comments = 'with one big loss and one big win against opponent (a better particular td net) and one not concerned match';
	$matches = array();
	$opponentMatches = array();
	$match = new stdClass();
	$match->opponentPoints = 5;
	$match->points = 0;
	$match->opponentScore = 2;
	$match->score = 0;
	$opponentMatches[] = $match;
	$match = new stdClass();
	$match->opponentPoints = 0;
	$match->points = 5;
	$match->opponentScore = 0;
	$match->score = 3;
	$opponentMatches[] = $match;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $opponentMatches;
	$matches[99] = $dummyMatch;
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,-1,$comments);
	
	$comments = 'with one big loss and one big win against opponent (a worst particular td net) and one not concerned match';
	$matches = array();
	$opponentMatches = array();
	$match = new stdClass();
	$match->opponentPoints = 5;
	$match->points = 0;
	$match->opponentScore = 3;
	$match->score = 0;
	$opponentMatches[] = $match;
	$match = new stdClass();
	$match->opponentPoints = 0;
	$match->points = 5;
	$match->opponentScore = 0;
	$match->score = 2;
	$opponentMatches[] = $match;
	$dummyMatch = new stdClass();
	$dummyMatch->opponentPoints = 0;
	$dummyMatch->points = 5;
	$dummyMatch->opponentScore = 0;
	$dummyMatch->score = 2;
	$matches[2] = $opponentMatches;
	$matches[99] = $dummyMatch;
	test_compare_team_championship_elt(10,1,0,1,$matches,-1,$comments);
	test_compare_team_championship_elt(0,1,10,1,$matches,1,$comments);
	test_compare_team_championship_elt(10,3,10,1,$matches,-1,$comments);
	test_compare_team_championship_elt(10,1,10,3,$matches,1,$comments);
	test_compare_team_championship_elt(10,1,10,1,$matches,1,$comments);
}

function test_get_points_from_match_championship(){
	test_get_points_from_match_championship_elt(2,0,5);
	test_get_points_from_match_championship_elt(2,1,5);
	test_get_points_from_match_championship_elt(2,2,2);
	test_get_points_from_match_championship_elt(2,3,1);
	test_get_points_from_match_championship_elt(2,4,0);
	test_get_points_from_match_championship_elt(2,5,0);
}




?>