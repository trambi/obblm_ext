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

require_once 'class_extendedrankings_strategy_fabric.php';

class ExtendedRankings implements ModuleInterface
{

	protected $title = '';
	protected $template = array();
	protected $strategies = array();
	const SQL_TABLE = 'tournaments_strategies';
	const SQL_TOURNAMENT_FIELD = 'f_tour';
	const SQL_STRATEGY_NAME_FIELD = 'strategy';
	
	public static function getModuleAttributes()
	{	
	    return array(
        	'author'     => 'Bertrand MADET',
	        'moduleName' => 'Extended Rankings',
	        'date'       => '2014',
	        'setCanvas'  => true,
	    );
	}

	public static function getModuleTables()
	{
		return array(
	            SQL_TABLE =>
        	        array(
                	    SQL_TOURNAMENT_FIELD    => 'MEDIUMINT',
	                    SQL_STRATEGY_NAME_FIELD   => 'VARCHAR(64)',
                	)
        	);
	}

	public static function getModuleUpgradeSQL()
	{
	    return array();
	}

	public static function triggerHandler($type, $argv)
	{

	}
	
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function addToTemplate($key,$value)
	{
		$this->template[$key] = $value;
	}

	protected function renderForm()
	{
		$template = $this->template;
		$type='';
		if(true == isset( $_POST['arg_type']))
		{
			$type = $_POST['arg_type'];
		}
		else if(true == isset ($_GET['arg_type']) )
		{
			$type = $_GET['arg_type'];
		}
		$dId = -1;
		if(true == isset( $_POST['division_id']))
		{
			$dId = intval($_POST['division_id']);
		}
		else if(true == isset ($_GET['division_id']) )
		{
			$dId = intval($_GET['division_id']);
		}
		$tId = -1;
		if(true == isset( $_POST['tour_id']))
		{
			$tId = intval($_POST['tour_id']);
		}
		else if(true == isset ($_GET['tour_id']) )
		{
			$tId = intval($_GET['tour_id']);
		}

		echo '<form id="extendedrankings" method="POST">',"\n";
		echo '<label for="division_id">',$template['divisionLabel'],'</label>',"\n";
		echo '<select name="division_id" id="division_id" ';
		echo 'onchange="document.getElementById(\'tour_id\').value=0;';
		echo 'document.getElementById(\'arg_type\').value=',T_NODE_DIVISION,';';
		echo 'document.getElementById(\'extendedrankings\').submit();">',"\n";
		echo '<option value="0">------</option>',"\n";
		foreach($template['availableDivisions'] as $division)
		{
			echo '<option value="',$division->did,'"';
			if( (T_NODE_DIVISION == $type) && ($division->did == $dId) )
			{
				echo ' selected="selected"';
			}
			echo '>',$division->name,"</option>\n";
		}
		echo '</select>',"\n";
    	echo '<label for="tour_id">',$template['tournamentLabel'],'</label>';
    	echo '<select name="tour_id" id="tour_id" ';
		echo 'onchange="document.getElementById(\'division_id\').value=0;';
		echo 'document.getElementById(\'arg_type\').value=',T_NODE_TOURNAMENT,';';
		echo 'document.getElementById(\'extendedrankings\').submit();">',"\n";
		echo '<option value="0">------</option>',"\n";
		foreach($template['availableTournaments'] as $tournament)
		{
			echo '<option value="',$tournament->tour_id,'"';
			
			if( (T_NODE_TOURNAMENT == $type) && ($tournament->tour_id == $tId) )
			{
				echo ' selected="selected"';
				$template['tournament']->name = $tournament->name;
			}
			echo '>',$tournament->name,"</option>\n";
		}
		echo '</select>',"\n";
		echo '<input type="hidden" name="arg_type" id="arg_type" value="0" />',"\n";
		echo '</form>';
	}
	
	public function render()
	{
		$template = $this->template;
		title($this->title);
		echo '<p>',$template['introduction'],"</p>\n";
		
		$this->renderForm();
		
		if( 2 > count($this->tournaments) )
		{
			$tournament = $this->tournaments[0];
			echo '<h3>',$tournament->name,'</h3>';
			echo '<table>';
			ExtendedRankings::displayHeader($tournament->strategy);
#	        echo '<tr><th>#</th><th>Equipe</th><th>Coach</th><th>Matchs jou&eacute;s</th>';
#	        echo '<th>Victoires</th><th>Nuls</th><th>Petites d&eacute;faites</th><th>D&eacute;faites</th>';
#	        echo '<th>Points</th><th>Diff&eacute;rences de touchdowns</th></tr>';
        	
			ExtendedRankings::displayRanking($tournament->teams,$tournament->strategy);	
		        echo '</table>';
		}
		else
		{
	        	foreach($this->tournaments as $tournament){
				echo '<h3>',$tournament->name,'</h3>';
				echo '<table>';
				ExtendedRankings::displayHeader($tournament->strategy);
				ExtendedRankings::displayRanking($tournament->teams,$tournament->strategy);					echo '</table>';
			}
		}
		echo '<p>Temps pris : ',$template['time'],"s</p>\n";
	}
	
	public static function main($argv){
		global $lng;
		$beginning = microtime(true);
		$mod = new ExtendedRankings();
		$mod->setTitle($lng->getTrn('name', __CLASS__));
		$mod->addToTemplate('introduction',$lng->getTrn('introduction',__CLASS__));
		$mod->addToTemplate('divisionLabel',$lng->getTrn('divisionLabel',__CLASS__));
		$mod->addToTemplate('tournamentLabel',$lng->getTrn('tournamentLabel',__CLASS__));
		$mod->addToTemplate('submitButton',$lng->getTrn('common/submit'));
		$mod->addToTemplate('availableDivisions',Division::getDivisions(false));
		$mod->addToTemplate('availableTournaments',Tour::getTours(false));
		if( ( true == isset($_POST['arg_type'] ) ) || (true == isset($_GET['arg_type']) ) )  
		{
			$type = (isset($_POST['arg_type'])) ? $_POST['arg_type'] : $_GET['arg_type'];
			$template['tournaments'] = array();
			if( ( T_NODE_DIVISION == $type ) && ( ( true == isset($_POST['division_id']) ) || (true == isset($_GET['division_id']) ) ) )
			{
				$divId = isset($_POST['division_id']) ? intval($_POST['division_id']) : intval($_GET['division_id']) ;
	        		$division = new Division($divId);
        			$tournaments = array_reverse($division->getTours());
        			if( 1 == count($tournaments) )
        			{
        				$tournament = $tournaments[0];
					$mod->setRankingsForATournament($tournament->tour_id);
	        		}
        			else
        			{
					foreach($tournaments as $tournament){
	        				$mod->setRankingsForATournament($tournament->tour_id);
	        			}
	        		}
			}
			elseif( ( T_NODE_TOURNAMENT == $type ) && ( ( true == isset($_POST['tour_id']) ) || ( true == isset($_GET['tour_id']) ) ) )
			{
				$tournamentId = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : intval($_GET['tour_id']);
				$mod->setRankingsForATournament($tournamentId);
			}
		}
		$end = microtime(true);
		$mod->addToTemplate('time',$end - $beginning);
		$mod->render();
	}
	public static function displayHeader($strategy){
		echo '<tr><th>#</th><th>Equipe</th><th>Coach</th>';
		$rows = $strategy->getHeader();
		foreach ($rows as $name=>$key){
			echo '<th>',$name,'</th>';
		}
                echo '</tr>';
	}
	
	public static function displayRanking($teams,$strategy){
		$n = count($teams);
		for($i=0;$i<$n;$i++){
	    		$team = $teams[$i];
		    	$url = 'index.php?section=objhandler&type=1&obj='.T_OBJ_TEAM.'&obj_id='.$team->id;
		      	echo '<tr><td>',$i+1,'</td><td><a href="',$url,'">',$team->name,'</a></td><td>',$team->coach,'</td>';
			$rows = $strategy->getHeader();
			foreach ($rows as $name=>$key){
				echo '<td>',$team->$key,'</td>';
			}
	      		echo '</tr>';
	    }
	}

	public static function createMatchForTeamFromRow($row,$isFirst){
		$matchForTeam = new stdClass();
		(true == $isFirst?$index = 1:$index = 2);
		$opponentIndex = 3 - $index;
		$columnsFormat = array('ffactor'=>'ffactor%d',
								'income'=>'income%d',
								'id' => 'team%d_id',
								'score' => 'team%d_score',
								'smp' => 'smp%d',
								'cas'=> 'tcas%d',
								'fame'=> 'fame%d',
								'tv' => 'tv%d');
		foreach($columnsFormat as $key => $columnFormat){
			$column = sprintf($columnFormat,$index);
			$matchForTeam->$key = $row[$column];
		}
		$matchForTeam->round = $row['round'];
		$matchForTeam->opponentScore = $row['team'.$opponentIndex.'_score'];
		$matchForTeam->opponentCas = $row['tcas'.$opponentIndex];
		$matchForTeam->opponentId = $row['team'.$opponentIndex.'_id'];
		return $matchForTeam;
	}
	
	public static function createTeamFromRow($row,$isFirst){
		$team = new stdClass();
		($isFirst?$index = 1:$index = 2);
		$opponentIndex = 3 - $index;
        $team->id = $row['team'.$index.'_id'];
        $team->name = $row['team'.$index];
        $team->coach = $row['coach'.$index];
        $team->score = 0;
        $team->cas = 0;
        $team->fame = 0;
        $team->smp = 0;
        $team->opponentScore = 0;
		$team->opponentCas = 0;
		$team->points = 0;
		$team->tdNet = 0;
		$team->playedMatches = 0;
		$team->win = 0;
		$team->draw = 0;
		$team->small_loss = 0;
	    $team->loss = 0;
        $team->matches = array();
        return $team;
	}
	
	public static function addMatchToTeam($match,$team){
		$team->score += $match->score;
        $team->cas += $match->cas;
        $team->opponentScore += $match->opponentScore;
        $team->opponentCas += $match->opponentCas;
        $team->tdNet = $team->score - $team->opponentScore ;
        $team->fame += $match->fame;
        $team->smp += $match->smp;
        $team->points += $match->points;
        $team->playedMatches ++;
		if( true == isset($team->matches[$match->opponentId]) ){
        	$previousMatches = $team->matches[$match->opponentId];
        	if(true == is_array($previousMatches)){
        		$previousMatches[]=$match;
        	}else{
        		$previousMatch = $team->matches[$match->opponentId];
        		$previousMatches = array($previousMatch,$match);
        	}
        	$team->matches[$match->opponentId] = $previousMatches;
        }else{
        	$team->matches[$match->opponentId] = $match;	
        }
	}
	
	public function setRankingsForATournament($tournamentId){
		// Get the tournament		
		$tournament = new Tour($tournamentId);
        	$tournament->strategy = ExtendedRankingsStrategyFabric::getStrategy('foo');
		$noStrategy = get_class($tournament->strategy);
		
		// 	Get the tournament strategy
		$query = 'SELECT '.self::SQL_STRATEGY_NAME_FIELD;
		$query .= ' FROM '.self::SQL_TABLE;
		$query .= ' WHERE '.self::SQL_TOURNAMENT_FIELD.'='.$tournamentId;
		$result = mysql_query($query);
	        while( $row = mysql_fetch_assoc($result)  ){
        		$tournament->strategy = ExtendedRankingsStrategyFabric::getStrategy($row[self::SQL_STRATEGY_NAME_FIELD]);
        }
		mysql_free_result($result);
		$teams = array();
		if ( TRUE == is_a($tournament->strategy,$noStrategy) )
		{
			$query = 'SELECT t.team_id id,t.name name,t.owned_by_coach_id coach_id,';
			$query .= 'mvt.pts points,c.name coach,mvt.won win,mvt.lost loss,';
			$query .= 'mvt.draw draw,mvt.played playedMatches,mvt.ga,mvt.gf';
			$query .= ' FROM mv_teams mvt';
			$query .= ' INNER JOIN teams t ON mvt.f_tid=t.team_id';
			$query .= ' INNER JOIN coaches c ON t.owned_by_coach_id=c.coach_id';
			$query .= ' WHERE mvt.f_trid='.intval($tournamentId,10);
			$query .= ' ORDER BY mvt.pts DESC';
			$result = mysql_query($query);
			while( $row = mysql_fetch_assoc($result) ){
				$team = new stdClass();
				foreach($row as $attr=>$value){
					$team->$attr=$value;
				}
				$team->tdNet = $team->gf - $team->ga;
				$team->name = mb_convert_encoding($team->name,'UTF-8');
				$team->coach = mb_convert_encoding($team->coach,'UTF-8');
				$teams[]=$team;
			}
		}
		else
		{
		// 	Get the matches
		$query = 'SELECT m.*,t1.name team1,t2.name team2,c1.name coach1,c2.name coach2 ';
		$query .= 'FROM matches m ';
		$query .= 'INNER JOIN teams t1 ON t1.team_id=m.team1_id ';
		$query .= 'INNER JOIN teams t2 ON t2.team_id=m.team2_id ';
		$query .= 'INNER JOIN coaches c1 ON c1.coach_id=t1.owned_by_coach_id ';
		$query .= 'INNER JOIN coaches c2 ON c2.coach_id=t2.owned_by_coach_id ';
		$query .= 'WHERE m.f_tour_id = '.intval($tournamentId,10).' AND m.date_played IS NOT NULL';
		$result = mysql_query($query);
        	while( $row = mysql_fetch_assoc($result) ){
	        	$matchForTeam1 = ExtendedRankings::createMatchForTeamFromRow($row,true);
        		$matchForTeam2 = ExtendedRankings::createMatchForTeamFromRow($row,false);

	        	// Depends of strategy
				$matchForTeam1->points = $tournament->strategy->getPointsFromMatch($matchForTeam1);
				$matchForTeam1->opponentPoints = $tournament->strategy->getPointsFromMatch($matchForTeam2);
				$matchForTeam2->points = $matchForTeam1->opponentPoints;
				$matchForTeam2->opponentPoints = $matchForTeam1->points;
        	
        			if(isset($teams[$matchForTeam1->id])){
		        		$team1 = $teams[$matchForTeam1->id];
        			}else{
	        		$team1 = ExtendedRankings::createTeamFromRow($row,true);
        			}
		        	ExtendedRankings::addMatchToTeam($matchForTeam1,$team1);
        			$tournament->strategy->addMatchToTeamCustom($matchForTeam1,$team1);
		        	$teams[$team1->id]=$team1;
        	
		        	if(isset($teams[$matchForTeam2->id])){
        				$team2 = $teams[$matchForTeam2->id];
		        	}else{
			                $team2 = ExtendedRankings::createTeamFromRow($row,false);
        			}
		        	ExtendedRankings::addMatchToTeam($matchForTeam2,$team2);
        			$tournament->strategy->addMatchToTeamCustom($matchForTeam2,$team2);
		        	$teams[$team2->id]=$team2;
        		}
	        usort($teams,array($tournament->strategy, 'compareTeam'));
	        mysql_free_result($result);
	}
        $tournament->teams = $teams;
        $this->tournaments[] = $tournament;
	}
	
}
?>
