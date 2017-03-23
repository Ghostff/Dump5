<?php
require 'src/PHP7.0/Dump.php';

$m = array(
	'fname' => null,
	'lname' => true
);
$c = array(
	'class' => 'cosc',
	'sch' => 'hccs',
	array(
		'td' => array(
			'm' => 8.5
		)
	));
$m[] = $c;


$country = array (
	array("U" => array(
		'United States',
		'United Kingdom',
		'Ukraine'
	)
));


$yes = 'yes';
$no = 'no';

class Test
{
	private $g = 'string';
	protected $r = 10;
	public $e = array('m', 'n');
	protected $f = false;
}

//Dump::config('name', '#F00');
//Dump::config('marg', 22);

new Dump('hey');