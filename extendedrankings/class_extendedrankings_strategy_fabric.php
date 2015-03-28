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

require_once 'class_extendedrankings_championship_strategy.php';
require_once 'class_extendedrankings_cup_strategy.php';
require_once 'class_extendedrankings_no_strategy.php';

class ExtendedRankingsStrategyFabric
{
	static public function getStrategy($strategyName)
	{
		
		if(ExtendedRankingsChampionshipStrategy::NAME == $strategyName)
		{
			$strategy = new ExtendedRankingsChampionshipStrategy();
		}
		elseif(ExtendedRankingsCupStrategy::NAME == $strategyName)
		{
			$strategy = new ExtendedRankingsCupStrategy();
		}
		else
		{
			$strategy = new ExtendedRankingsNoStrategy();	
		}
		return $strategy;
	}
}
?>
