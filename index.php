<?php
require 'function.php';

$m = array('fname' => null, 'lname' => true);
$c = array('class' => 'cosc', 'sch' => 'hccs', array('td' => array('m' => 8.5)));
$m[] = $c;


$country = array
         (
            array("N" => array('Nigeria', 'Namibia', 'Nauru', 'Nepal')),
            array("U" => array('Uganda', 'United States', 'United Kingdom', 'Ukraine')),
         );
         
         
$yes = 'yes';
$no = 'no';


new Dump($m, 'Hey', 10, null, $country);
new Dump($yes == $no);