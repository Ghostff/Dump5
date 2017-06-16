<?php

/**
 * Bittr
 *
 * @license
 *
 * New BSD License
 *
 * Copyright (c) 2017, ghostff community
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *      1. Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *      2. Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *      3. All advertising materials mentioning features or use of this software
 *      must display the following acknowledgement:
 *      This product includes software developed by the ghostff.
 *      4. Neither the name of the ghostff nor the
 *      names of its contributors may be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY ghostff ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL GHOSTFF COMMUNITY BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace Debug;

class Dump
{
    /**
     * @var string - null data type
     */
    private $_null = '6789f8';
    /**
     * @var string - variable type
     */
    private $_type = 'AAAAAA';
    /**
     * @var string - bool data type
     */
    private $_bool = 'bb02ff';
    /**
     * @var string - array data type
     */
    private $_array	= '000000';
    /**
     * @var string - float data type
     */
    private $_float = '9C6E25';
    /**
     * @var string - double data type
     */
    private $_double = '9C6E25';
    /**
     * @var string - string data  type
     */
    private $_string = '0000FF';
    /**
     * @var string - length of any data value
     */
    private $_lenght = '5BA415';
    /**
     * @var string - int data type
     */
    private $_integer = '1BAABB';
    /**
     * @var string - object data type
     */
    private $_object = '000000';
    /**
     * @var string - object properties visibility
     */
    private $_vsble = '741515';
    /**
     * @var string - object name
     */
    private $_object_name = '5ba415';
    /**
     * @var string - object property name
     */
    private $_obj_prop_name = '987a00';
    /**
     * @var string - object property name and value separator
     */
    private $_obj_prop_acc = 'f00000';
    /**
     * @var string - array of array key
     */
    private $_parent_arr = '59829e';
    /**
     * @var string - array of array accessor symbol
     */
    private $_parent_arr_acc = 'e103c4';
    /**
     * @var string - array
     */
    private $_child_arr = 'f07b06';
    /**
     * @var string - array value accessor symbol
     */
    private $_child_arr_acc = 'f00000';

    /**
     * @var array - runtime color buffer
     */
    private static $configurations = [];

    private static $cli_sub = 0;

    private static $use = '';


    private function isCLI(): bool
    {
        $use = self::$use;
        if ($use != '')
        {
            return ($use == 'cgi') ? false : true;
        }
        return ((substr(PHP_SAPI, 0, 3) === 'cli') && ! isset($_SERVER['REMOTE_ADDR']));
    }


    /**
     * Sets script execution interface.
     *
     * @param string $type
     */
    public static function use(string $type)
    {
        if($type != 'cgi' && $type != 'cli')
        {
            throw new \RuntimeException('use argument must be "cli" or "cgi"');
        }

        self::$use = $type;

    }

    /**
     * Dump constructor.
     */
    public function __construct()
    {
        if (self::$configurations !== [])
        {
            foreach (self::$configurations as $name => $value)
            {
                $_name = '_' . $name;
                if (property_exists($this, $_name))
                {
                    $this->{$_name} = $value;
                }
                else
                {
                    throw new RuntimeException('property ' . $name . ' does not exist');
                }
            }
        }

        $bt = debug_backtrace();
        $file = $bt[0]['file'] . '(line:' . $bt[0]['line'] . ')';

        if ($this->isCLI())
        {
            $dump = $this->formatCLI(func_get_args());
            echo $file . "\n" . $dump;
        }
        else
        {
            $file = '<span class="type" style="font-size:10px;">' . $file . '</span><br />';
            echo  '<code>' . $file . $this->formatCGI(func_get_args()) . '</code>';
        }

    }


    /**
     * updates color properties value
     *
     * @param string $name
     * @param string $new_value
     */
    public static function set(string $name, string $new_value)
    {
        self::$configurations[$name] = $new_value;
    }

    /**
     * object argument format for cgi
     *
     * @param $objects
     * @return string
     */
    private function objectsCGI($objects): string
    {
        $obj = new \ReflectionObject($objects);

        $temp = '<span class="object" style="font-weight:bold;color:#' . $this->_object . '">object -></span>';
        $format = '<div style="padding-left:20px;" class="obj_prop">';
        $size = 0;

        foreach ($obj->getProperties() as $size => $prop)
        {
            if ($prop->isPrivate())
            {
                $format .= '<span class="private" style="color:#' . $this->_vsble . '">private&nbsp;&nbsp; </span>';
            }
            elseif ($prop->isProtected())
            {
                $format .= '<span class="protected" style="color:#' . $this->_vsble . '">protected </span>';
            }
            elseif ($prop->isPublic())
            {
                $format .= '<span class="public" style="color:#' . $this->_vsble . '">public&nbsp;&nbsp;&nbsp; </span>';
            }

            $format .= '<span class="_obj_prop_name" style="color:#' . $this->_obj_prop_name . '">' . $prop->getName() . '</span>';
            $format .= '<span class="obj_prop_accessor" style="color:#' . $this->_obj_prop_acc . '"> : </span>';

            $prop->setAccessible(true);
            $format .= $this->formatCGI([$prop->getValue($objects)]);
            $size++;
        }

        $name =  '(' . $obj->getName() . ')';
        $temp .= '<span class="object" style="font-style:italic;color:#' . $this->_object_name . '">' . $name . '</span>';
        $temp .= '<span class="lenght" style="color:#' . $this->_lenght . '">';
        $temp .= '(size=' . ($size) . ')</span>';

        $temp .= $format . '</div>';
        return $temp;
    }

    /**
     * object argument format for cli
     *
     * @param $objects
     * @return string
     */
    private function objectsCLI($objects): string
    {
        $obj = new \ReflectionObject($objects);

        $temp = 'object ->';
        $format = '';
        $size = 0;

        self::$cli_sub += 3;
        $padding = str_repeat(' ', self::$cli_sub);
        foreach ($obj->getProperties() as $size => $prop)
        {
            if ($prop->isPrivate())
            {
                $format .= $padding . 'private   ';
            }
            elseif ($prop->isProtected())
            {
                $format .= $padding . 'protected ';
            }
            elseif ($prop->isPublic())
            {
                $format .= $padding . 'public    ';
            }

            $format .= $prop->getName() . ' : ';
            $prop->setAccessible(true);
            $format .= $this->formatCLI([$prop->getValue($objects)]);
            $size++;
        }
        self::$cli_sub -= 3;

        $name =  '(' . $obj->getName() . ')';
        $temp .=  $name . ' (size=' . ($size) . ') ' . "\n";

        $temp .= $format . "\n";
        return $temp;
    }

    /**
     * formats argument for cgi
     *
     * @param array $arguments
     * @param bool $array_loop
     * @return string
     */
    private function formatCGI(array $arguments, bool $array_loop = false): string
    {
        $format = '';
        foreach ($arguments as $arg)
        {
            $type = gettype($arg);
            if ($type == 'string')
            {
                $arg =  htmlspecialchars($arg);
                $format .= '<span class="string" style="color:#' . $this->_string . '">\'' . $arg . '\'</span>';
                $format .= '<span class="lenght" style="color:#' . $this->_lenght . '">';
                $format .= '(length=' . strlen($arg) . ')</span>';
                $format .= '<span class="type" style="font-size:10px;margin-left:7px;color:#' . $this->_type . '">';
                $format .= $type . '</span>';
            }
            elseif ($type == 'integer')
            {
                $format .= '<span class="integer" style="color:#' . $this->_integer . '">' . $arg . '</span>';
                $format .= '<span class="type" style="font-size:10px;margin-left:7px;color:#' . $this->_type . '">';
                $format .= $type . '</span>';
            }
            elseif ($type == 'boolean')
            {
                $arg = ($arg) ? 'true' : 'false';
                $format .= '<span class="bool" style="color:#' . $this->_bool . '">' . $arg . '</span>';
                $format .= '<span class="type" style="font-size:10px;margin-left:7px;color:#' . $this->_type . '">';
                $format .= $type . '</span>';
            }
            elseif ($type == 'double')
            {
                $format .= '<span class="double" style="color:#' . $this->_double . '">' . $arg . '</span>';
                $format .= '<span class="type" style="font-size:10px;margin-left:7px;color:#' . $this->_type . '">';
                $format .= $type . '</span>';
            }
            elseif ($type == 'NULL')
            {
                $format .= '<span class="null" style="color:#' . $this->_null . '">null</span>';
                $format .= '<span class="type" style="font-size:10px;margin-left:7px;color:#' . $this->_type . '">';
                $format .= $type . '</span>';
            }
            elseif ($type == 'float')
            {
                $format .= '<span class="float" style="color:#' . $this->_float . '">' . $arg . '</span>';
                $format .= '<span class="type" style="font-size:10px;margin-left:7px;color:#' . $this->_type . '">';
                $format .= $type . '</span>';
            }
            elseif ($type == 'array')
            {
                if ( ! $array_loop)
                {
                    $format .= '<span class="string" style="font-weight:bold;color:#' . $this->_array . '">array</span>';
                    $format .= '<span class="lenght" style="margin:0 5px;color:#' . $this->_lenght . '">';
                    $format .= '(size=' . count($arg) . ')</span>';
                    $format .= '<span class="string" style="font-weight:bold;color:#' . $this->_array . '">[</span>';
                    $format .= '<div class="arr_content" style="padding-left:20px;">';
                }

                foreach ($arg as $key => $value)
                {
                    $key = htmlspecialchars($key);
                    if ( is_array($value))
                    {
                        $format .= '<span class="string" style="color:#' . $this->_parent_arr . '">\'' . $key . '\'</span>';
                        $format .= '<span class="string" style="color:#' . $this->_parent_arr_acc . '"> = </span>';

                        $format .= '<span class="string" style="font-weight:bold;color:#' . $this->_array . '">array</span>';
                        $format .= '<span class="lenght" style="margin:0 5px;color:#' . $this->_lenght . '">';
                        $format .= '(size=' . count($value) . ')</span>';
                        $format .= '<span class="string" style="color:#' . $this->_array . '">{</span>';
                        $format .= '<div class="arr_content" style="padding-left:20px;">';

                        $format .= $this->formatCGI([$value], true);

                        $format .= '</div>';
                        $format .= '<span class="string" style="color:#' . $this->_array . '">}</span><br />';
                    }
                    else
                    {
                        $format .= '<span class="string" style="color:#' . $this->_child_arr . '">\'' . $key . '\'</span>';
                        $format .= '<span class="string" style="color:#' . $this->_child_arr_acc . '"> => </span>';
                        $format .= $this->formatCGI([$value], true);
                        $format .= '<br />';
                    }
                }

                if ( ! $array_loop)
                {
                    $format .= '</div>';
                    $format .= '<span class="string" style="font-weight:bold;color:#' . $this->_array . '">]</span>';
                }
            }
            elseif ($type == 'object')
            {
                $format .= $this->objectsCGI($arg);
            }

            if ( ! $array_loop)
            {
                $format .= '<br />';
            }
        }
        return str_replace('<br /></div><br />', '<br /></div>', nl2br($format));
    }

    /**
     * formats argument for cli
     *
     * @param array $arguments
     * @param bool $array_loop
     * @return string
     */
    private function formatCLI(array $arguments, bool $array_loop = false): string
    {
        $format = '';
        $nl = "\n";
        foreach ($arguments as $arg)
        {
            $type = gettype($arg);
            if ($type == 'string')
            {
                $format .= '\'' . $arg . '\' (length=' . strlen($arg) . ') string';
            }
            elseif ($type == 'integer')
            {
                $format .= $arg . ' int';
            }
            elseif ($type == 'boolean')
            {
                $arg = ($arg) ? 'true' : 'false';
                $format .=  $arg . ' bool   ';
            }
            elseif ($type == 'double')
            {
                $format .= $arg . ' double   ';
            }
            elseif ($type == 'NULL')
            {
                $format .= 'null NULL';
            }
            elseif ($type == 'float')
            {
                $format .= $arg . ' float';
            }
            elseif ($type == 'array')
            {
                if ( ! $array_loop)
                {
                    $format .= 'array (size=' . count($arg) . ') [' . $nl;
                }

                self::$cli_sub += 3;
                $padding = str_repeat(' ', self::$cli_sub);
                foreach ($arg as $key => $value)
                {
                    if ( is_array($value))
                    {
                        $format .= $padding . '\'' . $key . '\' = array (size=' . count($value) . ') {' . $nl;
                        $format .= $this->formatCLI([$value], true);
                        $format .= $padding . '}' . $nl;
                    }
                    else
                    {
                        $format .=  $padding . '\'' . $key . '\' => ' . $this->formatCLI([$value], true) . $nl;
                    }
                }
                self::$cli_sub -= 3;

                if ( ! $array_loop)
                {
                    $format .= str_repeat(' ', self::$cli_sub) . ']';
                }
            }
            elseif ($type == 'object')
            {
                $format .= $this->objectsCLI($arg);
            }

            if ( ! $array_loop)
            {
                $format .= $nl;
            }
        }
        return $format;
    }
}
