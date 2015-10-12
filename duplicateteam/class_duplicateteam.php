<?php

/*
 *  Copyright (c) Bertrand MADET <bertrand.madet@gmail.com> 2015. All Rights Reserved.
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
	        'date'       => '2015',
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

	public function renderForm()
	{
        
		$template = $this->template;
		echo '<script>',"\n";
        echo '$(document).ready(function(){',"\n";
        echo '  var options,finalCoach,team;',"\n";

        echo '  options = {',"\n";
        echo '    minChars:2, ',"\n";
        echo '    serviceUrl:\'handler.php?type=autocomplete&obj=',T_OBJ_COACH,'\'',"\n";
        echo '  };',"\n";
        echo '  finalCoach = $(\'#finalCoach\').autocomplete(options);',"\n";
        echo '  options = {',"\n";
        echo '    minChars:2,',"\n";
        echo '    serviceUrl:\'handler.php?type=autocomplete&obj=',T_OBJ_TEAM,'\'',"\n";
        echo '  };',"\n";
        echo '  team = $(\'#initialTeam\').autocomplete(options);',"\n";
        echo '});',"\n";
        echo '</script>',"\n";

		echo '<form id="duplicateteam" method="POST">',"\n";
		echo '<label for="finalCoach">',$template['finalCoachLabel'],'</label>',"\n";
        echo '<input id="finalCoach" type="text" name="finalCoach" size="30" maxlength="50" />',"\n";
        echo '<label for="initialTeam">',$template['initialTeamLabel'],'</label>',"\n";
        echo '<input id="initialTeam" type="text" name="initialTeam" size="30" maxlength="50" onblur="$(\'#initialTeam\').value=$(\'#finalTeam\').value" />',"\n";
        echo '<label for="finalTeam">',$template['finalTeamLabel'],'</label>',"\n";
        echo '<input id="finalTeam" type="text" name="finalTeam" size="30" maxlength="50" />',"\n";
		echo '<input type="submit" value="',$template['submitButton'],'" name="Submit" >',"\n";
		echo '<input type="hidden" value="1" name="submitted" >',"\n";
		echo '</form>';
	}
    
    public function renderErrors(){
        $template = $this->template;
        echo $template['errorLabel'],$template['errors'];
    }
    
    public function renderDuplicate(){
        $template = $this->template;
        echo 'Duplicated ';
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
            $mod->renderForm();
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
                $control = DuplicateTeam::duplicate($initialTeam,$finalTeam,$finalCoach);
                if( FALSE === $control){
                    $errors[] = $lng->getTrn('duplicateError',__CLASS__);
                }
            }
            if( 0 !== count($errors) ){
                $mod->addToTemplate('errorLabel',$lng->getTrn('errorLabel',__CLASS__));
                $errorString = ucfirst(implode(",", $errors));
                $mod->addToTemplate('errors',$errorString);
                $mod->renderErrors();
            }else{
                $mod->renderDuplicate();
            }
        }
	}
	
	
}
?>
