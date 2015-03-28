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

class ExtendedRankingsNoStrategy implements ExtendedRankingsStrategy
{

	const NAME = 'no';
	const DESCRIPTION = 'no_strategy_description';
	
	public function compareTeam($team1,$team2){
		$cmp =0;
		return $cmp;
	}

	public function getPointsFromMatch($matchForTeam){
		$points = 0;
		return $points;
	}

	public function addMatchToTeamCustom($match,$team)
	{
	}

	public function getHeader()
	{
		return array('Matchs jou&eacute;s'=>'playedMatches',
				'Victoires'=>'win',
				'Nuls'=>'draw',
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
