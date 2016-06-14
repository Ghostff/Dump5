<?php
require 'function.php';

$m = array('fname' => null, 'lname' => true);
$c = array('class' => 'cosc', 'sch' => 'hccs', array('td' => array('m' => 8.5)));
$m[] = $c;


$country = array
         (
		 	array("U" => array('United States', 'United Kingdom', 'Ukraine')),
            array("N" => array('Namibia', 'Nauru', 'Nepal')), 
         );
         
         
$yes = 'yes';
$no = 'no';


new Dump($m, 'Hey', 10, null, $country);
new Dump($yes == $no);