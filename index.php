<?php
require 'function.php';
$var = new Controls();

$m = array('fname' => null, 'lname' => true);
$c = array('class' => 'cosc', 'sch' => 'hccs', array('td' => array('m' => 8.5)));
$m[] = $c;


$country = array
  (
  array("N" => array('Nigeria', 'Namibia', 'Nauru', 'Nepal')),
  array("U" => array('Uganda', 'United States', 'United Kingdom', 'Ukraine')),
  );


echo $var->dump($m, 'Hey', 10, null, true, $country);