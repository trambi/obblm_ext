<?php
/*
 *  Copyright (c) Bertrand MADET <bertrand.madet@gmail.com> 2012. All Rights Reserved.
 *
 *
 *  This file is part of OBBLM.
 *
 *  OBBLM is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  OBBLM is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once 'interface_extendedrankings_strategy.php';

class ExtendedRankingsChampionshipStrategy implements ExtendedRankingsStrategy
{

	const NAME = 'championship';
	const WIN_POINTS = 5;
	const DRAW_POINTS = 2;
	const SMALL_LOSS_POINTS = 1;
	const LOSS_POINTS = 0;
	const TD_GAP_FOR_SMALL_LOSS = 1;
	const DESCRIPTION = 'championship_strategy_description';
	
	public function compareTeam($team1,$team2){
		$cmp =0;
	
		if($team1->points > $team2->points){
				$cmp = -1;
		}elseif($team1->points < $team2->points){
				$cmp = 1;
		}elseif($team1->points == $team2->points){
			if($team1->tdNet > $team2->tdNet){
				$cmp = -1;
			}elseif ($team1->tdNet < $team2->tdNet){
				$cmp = 1;
			}else{
				if(TRUE == isset($team1->matches[$team2->id])){
					$opponentMatches = $team1->matches[$team2->id];
					$confrontationPoints = 0;
					$confrontationTd = 0;
					if( true == is_array($opponentMatches) ){
						$n = count($opponentMatches);
						for($i=0;$i<$n;$i++){
							$confrontationPoints += $opponentMatches[$i]->points - $opponentMatches[$i]->opponentPoints; 
							$confrontationTd += $opponentMatches[$i]->score - $opponentMatches[$i]->opponentScore;
						}
					}elseif($opponentMatches instanceof stdClass){
						$confrontationPoints = $opponentMatches->points - $opponentMatches->opponentPoints;
						$confrontationTd += $opponentMatches->score - $opponentMatches->opponentScore;
	
					}else{
						$cmp = 0;
					}
					if(0 < $confrontationPoints){
						$cmp = -1;
					}elseif (0 > $confrontationPoints ){
						$cmp = 1;
					}elseif(0 < $confrontationTd){
						$cmp = -1;
					}elseif (0 > $confrontationTd ){
						$cmp = 1;
					}else{
						$cmp = 0;
					}
				}
			}
		}
		return $cmp;
	}

	public function getPointsFromMatch($matchForTeam){
		$points = 0;
		if( ($matchForTeam->score) > ($matchForTeam->opponentScore) ){
			$points = self::WIN_POINTS;
		}elseif ( ($matchForTeam->score) == ($matchForTeam->opponentScore) ) {
			$points = self::DRAW_POINTS;
		}elseif ( ($matchForTeam->score + self::TD_GAP_FOR_SMALL_LOSS)  == $matchForTeam->opponentScore ){
			$points = self::SMALL_LOSS_POINTS;
		}else{
			$points = self::LOSS_POINTS;
		}
		return $points;
	}

	public function addMatchToTeamCustom($match,$team)
	{
		if( self::WIN_POINTS == $match->points )
        {
        	$team->win++;
        }
        elseif( self::DRAW_POINTS == $match->points )
        {
        	$team->draw++;
        }
        elseif( self::SMALL_LOSS_POINTS == $match->points )
        {
        	$team->small_loss++;
        }
        else
        {
        	$team->loss++;
        }
	}

	public function getHeader()
	{
		return array('Matchs jou&eacute;s'=>'playedMatches',
				'Victoires'=>'win',
				'Nuls'=>'draw',
				'Petites d&eacute;faites'=>'small_loss',
				'D&eacute;faites'=>'loss',
				'Points'=>'points',
				'Diff&eacute;rences de touchdowns'=>'tdNet');	
	}

	public function getDescription()
	{
		return self::DESCRIPTION;
	}
	
	
}
?>
