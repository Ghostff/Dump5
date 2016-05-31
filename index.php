<?php
require 'function.php';
$var = new Controls();

$m = array('fname' => null, 'lname' => true);
$c = array('class' => 'cosc', 'sch' => 'hccs', array('td' => array('m' => 8.5)));
$m[] = $c;
$g = array('user' => array('type' => array('name' => 90)));
$m[] = $g;





echo $var->dump($m, 'hey', 0, 'up', 0.2, '', false);