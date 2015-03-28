<?php
require('../../lib/class_module.php');
require('../../settings.php');
require('class_extendedrankings.php');
require('test_championship.php');
require('test_cup.php');

test_compare_team_championship();
test_get_points_from_match_championship();
test_get_points_from_match_cup();
?>
