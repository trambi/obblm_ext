<form id="duplicateteam" method="POST">
    <label for="finalCoach"><?php echo $template['finalCoachLabel'];?></label>
    <input id="finalCoach" type="text" name="finalCoach" size="30" maxlength="50" />
    <label for="initialTeam"><?php echo $template['initialTeamLabel'];?></label>
    <input id="initialTeam" type="text" name="initialTeam" size="30" maxlength="50" onblur="$('#initialTeam').value = $('#finalTeam').value" />
    <label for="finalTeam"><?php echo $template['finalTeamLabel'];?></label>
    <input id="finalTeam" type="text" name="finalTeam" size="30" maxlength="50" />
    <input type="submit" value="<?php echo $template['submitButton'];?>" name="Submit" />
    <input type="hidden" value="1" name="submitted" />
</form>

<script>
  $(document).ready(function(){
    var options,finalCoach,team;

    options = {
      minChars:2,
      serviceUrl:'handler.php?type=autocomplete&obj=<?php echo T_OBJ_COACH; ?>'
    };
    finalCoach = $('#finalCoach').autocomplete(options);
    options = {
      minChars:2,
      serviceUrl:'handler.php?type=autocomplete&obj=<?php echo T_OBJ_TEAM; ?>'
    };
    team = $('#initialTeam').autocomplete(options);
  });
</script>