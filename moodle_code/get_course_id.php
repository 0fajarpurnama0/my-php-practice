<?php
require_once('/var/www/html/fajar_moodle' . '/config.php');
$courses = get_courses();
print_r($courses);
