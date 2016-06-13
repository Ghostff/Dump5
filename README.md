# Pretty Data Dump
A pretty version of php var_dump

```php
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
			
			
$yes = 'yes';
$no = 'no';

#Note this take 1 to infinit arguments
#   new Dump([mixed vars ... (not supporting objects yet)] );

new Dump($m, 'Hey', 10, null, $country);
new Dump($yes == $no);

```
The above code outputs

![alt tag](https://github.com/Ghostff/pretty_data_dump.php/blob/master/SS.png)