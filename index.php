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

class Test
{
	private $g = 'string';
	protected $r = 10;
	public $e = array('m', 'n');
	protected $f = false;
}


new Dump(new Test, $m, 'Hey', 10, null, $country);