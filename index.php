<?php

include 'src/PHP5.3/Dump.php';



#Debug\Dump::using('cli');

class Foo
{
    private $string = 'string';
    protected $int = 10;
    public $array = array(
        'foo'   => 'bar'
    );
    protected static $bool = false;
}

$string = 'Foobar';
$array = array('foo', 'bar');
$int = 327626;
$double = 22.223;
$null = null;
$bool = true;


new Debug\Dump(new Foo, $string, $array, $int, $double, $null, $bool, array(
    'foo' => 'bar',
    'bar' => 'foo',
    array(
        'foo' => 'foobar',
        'bar_foo',
        2 => 'foo',
        'foo' => array(
            'barbar' => 55,
            'foofoo' => false,
            'foobar' => null,
    )
    )
));

new Debug\Dump(1 == '1', 1 === '1');