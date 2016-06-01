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

#Note this take 1 to infinit arguments
#   $var->dump($m ... );

echo $var->dump($m, 'Hey', 10, null, true, $country);

```
The above code outputs

![alt tag](https://github.com/Ghostff/pretty_data_dump.php/blob/master/SS.png)