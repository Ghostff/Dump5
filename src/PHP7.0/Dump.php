<?php

class Dump
{
	private $_type = 'AAAAAA';
	private $_array	= '000000';
	private $_float = '9C6E25';
	private $_double = '9C6E25';
	private $_string = '0000FF';
	private $_lenght = '5BA415';
	private $_integer = '1BAABB';
	private $_object = '000000';
	private $_visibility = '741515';
	private $_object_name = '5ba415';
	private $_single_arr_key = 'f07b06';
	private $_obj_prop_name = '987a00';
	private $_double_arr_key = '59829e';
	private $_single_arr_accessor = 'F00000';
	private $_double_arr_accessor = 'e103c4';
	private $_obj_prop_accessor = 'F00000';
	
	private $_room_name = 'PARRENT_\ARRAY_\CONTENTS_\GOES_\HERE_';
	private $tag = 'code';
	
	
	public function __construct()
	{
		echo $this->format(func_get_args());
	}
	
	private function span(string $content, string $color, string $class, string $optional = null): string
	{
		$format = '<span class="%s" style="color:#%s;%s">%s</span>';
		return sprintf($format, $class, $color, $optional, $content);
	}
	
	private function length(int $lenght): string
	{
		$lenght = '(length=' . $lenght . ')';
		return $this->span($lenght, $this->_lenght, 'length');
	}
	
	private function type(string $type): string
	{
		return $this->span($type, $this->_type, 'type', 'font-size:10px;margin-left:10px;');
	}
	
	private function array_parent(int $size = 0, int $type = 0): string
	{
		$_array = '';
		$tag = ($type == 0) ? ['[', ']'] : ['{', '}']; 
		
		$_array = $this->span('array', $this->_array, 'array', 'font-weight:bold;margin-right:5px;');
		$_array .= $this->length($size);
		$_array .= $this->span($tag[0], $this->_array, 'array', $type == 0 ? 'font-weight:bold;' : '');
		
		if ($size == 0)
		{
			$_array .= '<br />';
		}
		for ($i = 0; $i < $size; $i++)
		{
			$counter = $this->_room_name . $i;
			$_array .= sprintf('<div style="padding-left:20px;"/>%s</div>', $counter);
		}
						
		$_array .= $this->span($tag[1], $this->_array, 'array', $type == 0 ? 'font-weight:bold;' : '');
		return $_array;
	}
	
	private function array_key(string $key, int $type = 0): string
	{
		$_array = '';
		if ($type == 0)
		{
			$_array .= $this->span("'$key'", $this->_single_arr_key, 'single_arr_key');
			$_array .= $this->span(' => ', $this->_single_arr_accessor, 'single_arr_accessor');
		}
		else
		{
			$_array .= $this->span("'$key'", $this->_double_arr_key, 'double_arr_key');
			$_array .= $this->span(' = ', $this->_double_arr_accessor, 'double_arr_accessor');
		}
		return $_array;
	}
	
	private function objects($objects): string
	{
		$obj = new \ReflectionObject($objects);
		$temp = $this->span('object', $this->_object, 'object', 'font-weight:bold;');
		
		$properties = '<div style="padding-left:20px;" class="obj_prop">';
		foreach ($obj->getProperties() as $size => $prop)
		{	
			if ($prop->isPrivate())
			{
				$properties .=  $this->span('private&nbsp;&nbsp; ', $this->_visibility, 'private');
            }
            elseif ($prop->isProtected())
			{
				$properties .=  $this->span('protected ', $this->_visibility, 'protected');
            }
            elseif ($prop->isPublic())
			{
				$properties .=  $this->span('public&nbsp;&nbsp;&nbsp; ', $this->_visibility, 'public');
            }
			
			$properties .=  $this->span($prop->getName(), $this->_obj_prop_name, 'obj_prop_name');
			$properties .=  $this->span(' : ', $this->_obj_prop_accessor, 'obj_prop_accessor');
			
			$prop->setAccessible(true);
			$properties .= $this->format([$prop->getValue($objects)]);
			
		}
		
		$name =  '(' . $obj->getName() . ')';
		$temp .= $this->span($name, $this->_object_name, 'object_name', 'font-style:italic;');
		$temp .= $this->length($size + 1);
		$temp .= $properties . '</div>';
		return $temp;
	}
	
	private function format(array $arguments, bool $array_loop = false): string
	{
		$format = null;
		foreach ($arguments as $arg)
		{
			#$arg = ($array_loop) ? $arguments : $arg;
			$type = gettype($arg);
			
			if ($type == 'string')
			{
				$format .= $this->span("'$arg'", $this->_string, 'string');
				$format .= $this->length(strlen($arg));
				$format .= $this->type($type);
			}
			elseif ($type == 'integer')
			{
				$format .= $this->span($arg, $this->_integer, 'int');
				$format .= $this->type($type);
			}
			elseif ($type == 'double')
			{
				$format .= $this->span($arg, $this->_double, 'double');
				$format .= $this->type($type);
			}
			elseif ($type == 'float')
			{
				$format .= $this->span($arg, $this->_float, 'float');
				$format .= $this->type($type);
			}
			elseif ($type == 'array')
			{	
				$i = 0;
				$format .= $this->array_parent(count($arg), ($array_loop) ? 1 : 0);
				foreach ($arg as $key => $value)
				{
					if ( empty($value))
					{
						$_format = $this->array_key($key, 1);
						$_format .= $this->array_parent(count($value), 1);
					}
					else
					{
						if (is_array($value))
						{
							$_format = $this->array_key($key, 1);
							foreach ($value as $keys => $val)
							{
								if (is_array($val))
								{
									$_format = $this->format([$value], true);
								}
								else
								{
									$_format = $this->format([$value], true);
								}
							}
							$_format = $this->array_key($key, 1) . $_format;
						}
						else
						{
							$_format = $this->array_key($key);
							$_format .= $this->format([$value]);
						}
					}
					$format = str_replace($this->_room_name . $i, $_format, $format);
					$i++;
					$this->last_exec = $i;
				}
				
			}
			elseif ($type == 'object')
			{
				$format .= $this->objects($arg);
			}
			$format .= '<br />';
		}
		return sprintf('<%1$s>%2$s</%1$s>', $this->tag, $format);
	}
}