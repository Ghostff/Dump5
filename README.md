# Pretty Data Dump
A pretty version of php var_dump

```php
require 'src/Dump.php';

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

#uses func_get_args (No argument limit)
new Dump(new Test, $m, 'Hey', 10, null, $country);
new Dump($yes == $no);


```
The above code outputs

![alt tag](https://github.com/Ghostff/pretty_data_dump.php/blob/master/SS.png)
