<?php

/*
 *  Copyright (c) Bertrand MADET <bertrand.madet@gmail.com> 2015-2016. All Rights Reserved.
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


class DuplicateTeam implements ModuleInterface
{

	protected $title = '';
	protected $template = array();
	
	public static function getModuleAttributes()
	{	
	    return array(
        	'author'     => 'Bertrand MADET',
	        'moduleName' => 'Duplicate Team',
	        'date'       => '2016',
	        'setCanvas'  => true,
	    );
	}

	public static function getModuleTables()
	{
		return array();
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

  public function duplicateByName($initialTeamName,$finalTeamName,$finalCoachName)
  {
    // get team by name $initialTeamName
    $teamId = get_alt_col('teams', 'name', $initialTeamName, 'team_id');
    $teamToDuplicate = new Team($teamId);
    
    //get coach id in addition of name $finalCoachName 
    return $this->duplicate($teamToDuplicate,get_alt_col('coaches', 'name', $finalCoachName, 'coach_id'),$finalTeamName);
  }
  
  protected function duplicate($initialTeam,$finalCoachId,$finalTeamName)
  {
    $finalTeamInput = array();
    foreach ( Team::$createEXPECTED as $expectedField){
      $finalTeamInput[$expectedField] = $initialTeam->$expectedField;
    }
    $finalTeamInput['name'] = $finalTeamName;
    $finalTeamInput['owned_by_coach_id'] = $finalCoachId;
    $finalTeamInput['won_0'] = $initialTeam->mv_won ;
    $finalTeamInput['lost_0'] = $initialTeam->mv_lost ;
    $finalTeamInput['draw_0'] = $initialTeam->mv_draw ;
    $finalTeamInput['played_0'] = $initialTeam->mv_won + $initialTeam->mv_lost + $initialTeam->mv_draw ;
    $finalTeamInput['wt_0'] = $initialTeam->wt_cnt; //Won tournaments
    $finalTeamInput['gf_0'] = $initialTeam->mv_gf; //Goals by team
    $finalTeamInput['ga_0'] = $initialTeam->mv_ga; //Goals against team
    $finalTeamInput['imported'] = 1;
    list($createTeamStatus, $teamId) = Team::create($finalTeamInput);
    if( $createTeamStatus !== 0 ){
      return FALSE;
    }
    
    $playersToDuplicate = $initialTeam->getPlayers();
    foreach($playersToDuplicate as $playerToDuplicate){
      if ( ( false === $playerToDuplicate->is_dead ) && ( false === $playerToDuplicate->is_sold ) && ( false === $playerToDuplicate->is_journeyman ) ){
        $this->duplicatePlayer($playerToDuplicate,$teamId);
      }
    }
    $initialTeam->setRetired(TRUE);
  }
    
  protected function duplicatePlayer($playerToDuplicate,$teamId){
    $playerDuplicated = array('name'=>$playerToDuplicate->name,
                              'team_id'=>$teamId,
                              'nr'=>$playerToDuplicate->nr,
                              'f_pos_id'=>$playerToDuplicate->f_pos_id);
    return Player::create( $playerDuplicated, array('force' => true, 'free' => true) );
  }
    
  public function render($file){
    if ( TRUE === in_array($file,array('form','result')) ){
      $template = $this->template;
      include('templates/'.$file.'_html.php');
    }
  }
		
	public static function main($argv){
		global $lng;
		$beginning = microtime(true);
		$mod = new DuplicateTeam();
		$mod->setTitle($lng->getTrn('name', __CLASS__));
		$mod->addToTemplate('initialTeamLabel',$lng->getTrn('initialTeamLabel',__CLASS__));
		$mod->addToTemplate('finalCoachLabel',$lng->getTrn('finalCoachLabel',__CLASS__));
		$mod->addToTemplate('finalTeamLabel',$lng->getTrn('finalTeamLabel',__CLASS__));
		$mod->addToTemplate('submitButton',$lng->getTrn('common/submit'));
    if ( FALSE === isset($_POST['submitted']) ){
      $mod->render('form');
    }else{
      $errors = array();
      if( FALSE === isset($_POST['initialTeam'])){
        $errors[] = $lng->getTrn('initialTeamUndefined',__CLASS__);
      }
      if( FALSE === isset($_POST['finalCoach'])){
        $errors[] = $lng->getTrn('finalCoachUndefined',__CLASS__);
      }
      if( FALSE === isset($_POST['finalTeam'])){
        $errors[] = $lng->getTrn('finalTeamUndefined',__CLASS__);
      }
      if( 0 === count($errors) ){
        $initialTeam = $_POST['initialTeam'];
        $finalTeam = $_POST['finalTeam'];
        $finalCoach = $_POST['finalCoach'];
        
        $control = $mod->duplicateByName($initialTeam,$finalTeam,$finalCoach);
        if( FALSE === $control){
          $errors[] = $lng->getTrn('duplicateError',__CLASS__);
        }
      }
      if( 0 !== count($errors) ){
        $errorString = ucfirst(implode(",", $errors));
        $mod->addToTemplate('status','error');
        $mod->addToTemplate('message',$lng->getTrn('errorLabel',__CLASS__).$errorString);
      }else{
        $mod->addToTemplate('status','info');
        $mod->addToTemplate('message','team "'.$initialTeam.'" duplicated and affected to "'.$finalCoach.'" with the name "'.$finalTeam.'"');
      }
      $mod->render('result');
    }
	}
}
?>
