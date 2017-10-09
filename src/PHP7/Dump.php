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

class Dump
{
    protected $isCli = false;

    private $indent = 0;

    private $nest_level = 20;

    private $pad_size = 3;

    private $output = null;

    private $isPosix = false;

    private $colors = [
        'string'                => ['0000FF', 'blue'],
        'integer'               => ['1BAABB', 'light_green'],
        'double'                => ['9C6E25', 'cyan'],
        'boolean'               => ['bb02ff', 'purple'],
        'null'                  => ['6789f8', 'white'],
        'type'                  => ['AAAAAA', 'light_gray'],
        'size'                  => ['5BA415', 'green'],
        'recursion'             => ['F00000', 'red'],

        'array'                 => ['000000', 'white'],
        'multi_array_key'       => ['59829e', 'yellow'],
        'single_array_key'      => ['f07b06', 'light_yellow'],
        'multi_array_arrow'     => ['e103c4', 'red'],
        'single_array_arrow'    => ['f00000', 'red'],

        'object'                => ['000000', 'white'],
        'property_visibility'   => ['741515', 'light_red'],
        'property_name'         => ['987a00', 'light_cyan'],
        'property_arrow'        => ['f00000', 'red'],

    ];

    private static $force_posix = false;

    private static $changes = [];

    /**
     * Foreground colors map
     * @var array
     */
    private $foregrounds = [
        'none'          => null,
        'black'         => 30,
        'red'           => 31,
        'green'         => 32,
        'yellow'        => 33,
        'blue'          => 34,
        'purple'        => 35,
        'cyan'          => 36,
        'light_gray'    => 37,
        'dark_gray'     => 90,
        'light_red'     => 91,
        'light_green'   => 92,
        'light_yellow'  => 93,
        'light_blue'    => 94,
        'light_magenta' => 95,
        'light_cyan'    => 96,
        'white'         => 97,
    ];

    /**
     * Background colors map
     * @var array
     */
    private $backgrounds = [
        'none'          => null,
        'black'         => 40,
        'red'           => 41,
        'green'         => 42,
        'yellow'        => 43,
        'blue'          => 44,
        'purple'        => 45,
        'cyan'          => 46,
        'light_gray'    => 47,
        'dark_gray'     => 100,
        'light_red'     => 101,
        'light_green'   => 102,
        'light_yellow'  => 103,
        'light_blue'    => 104,
        'light_magenta' => 105,
        'light_cyan'    => 106,
        'white'         => 107,
    ];

    /**
     * Styles map
     * @var array
     */
    private $styles = [
        'none'      => null,
        'bold'      => 1,
        'faint'     => 2,
        'italic'    => 3,
        'underline' => 4,
        'blink'     => 5,
        'negative'  => 7,
    ];


    /**
     * Dump constructor.
     */
    public function __construct()
    {
        if (substr(PHP_SAPI, 0, 3) == 'cgi')
        {
            $this->isCli = true;
            $this->setOutputStream(STDIN);
        }

        $this->colors = self::$changes + $this->colors;
        $this->output($this->evaluate(func_get_args()));
    }

    /**
     * Force debug to use posix, (For window users who are using tools like http://cmder.net/)
     */
    public static function d()
    {
        self::$force_posix = true;
        new self(func_get_args());
    }

    /**
     * Updates color properties value.
     *
     * @param string $name
     * @param array $value
     */
    public static function set(string $name, array $value)
    {
        self::$changes[$name] = $value;
    }


    /**
     * Assert code nesting doesn't surpass specified limit.
     *
     * @return bool
     */
    public function aboveNestLevel(): bool
    {
        return (count(debug_backtrace()) > $this->nest_level);
    }

    /**
     * Check if working under Windows
     *
     * @see http://stackoverflow.com/questions/738823/possible-values-for-php-os
     * @return bool
     */
    private function isWindows(): bool
    {
        return
            (defined('PHP_OS') && (substr_compare(PHP_OS, 'win', 0, 3, true) === 0)) ||
            (getenv('OS') != false && substr_compare(getenv('OS'), 'windows', 0, 7, true));
    }

    /**
     * Check if a resource is an interactive terminal
     *
     * @see https://github.com/auraphp/Aura.Cli/blob/2.x/src/Stdio/Handle.php#L117
     * @param  resource  $resource
     * @return bool
     */
    private function isPosix($resource): bool
    {
        if (self::$force_posix) {
            return true;
        }
        // Windows
        if ($this->isWindows())
        {
            return false;
        }

        // disable posix errors about unknown resource types
        if (function_exists('posix_isatty'))
        {
            set_error_handler(function () {});
            $isPosix = posix_isatty($resource);
            restore_error_handler();
            return $isPosix;
        }

        return false;
    }

    /**
     * Sets output stream to write to
     *
     * @param $resource
     * @throws Exception
     */
    public function setOutputStream($resource)
    {
        if (! is_resource($resource))
        {
            throw new Exception('Invalid resource');
        }

        // Detect posix terminal
        $this->isPosix = $this->isPosix($resource);
        $this->output = $resource;
    }

    /**
     * Format string using ANSI escape sequences
     *
     * @param  string $string
     * @param  string $format defaults to 'none|none|none'
     * @return string
     */
    private function format(string $string, string $format = null): string
    {
        // format only for POSIX
        if (! $format || ! $this->isPosix)
        {
            return $string;
        }

        $format = $format ? explode('|', $format) : [];

        $code = array_filter([
            $this->backgrounds[$format[1] ?: null] ?: null,
            $this->styles[$format[2] ?: null] ?: null,
            $this->foregrounds[$format[0] ?: null] ?: null,
        ]);

        $code = implode(';', $code);

        return "\033[{$code}m{$string}\033[0m";
    }

    /**
     * Writes dump to console.
     *
     * @param $message
     * @param null|string $format
     */
    public function write(string $message, string $format = null)
    {
        fwrite($this->output, $this->format($message, $format));
    }

    /**
     * Outputs formatted dump files.
     *
     * @param string $data
     */
    private function output(string $data): void
    {
        # Gets line
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($bt as $key => $value)
        {
            if ($value['file'] != __FILE__)
            {
                unset($bt[$key]);
            }
            else
            {
                $bt = $bt[((int) $key) + 1];
                break;
            }
        }
        $file = $bt['file'] . '(line:' . $bt['line'] . ')';
        if (! $this->isCli)
        {
            echo '<code><small>' . $file . '</small><br />' . $data . '</code>';
        }
        else
        {
            $this->write($data);
        }
    }

    /**
     * Sets string color based on sapi.
     *
     * @param $value
     * @param string $name
     * @return string
     */
    private function color($value, string $name): ?string
    {
        if (! $this->isCli)
        {
            if ($name == 'type')
            {
                return '<small style="color:#' . $this->colors[$name][0] . '">' . $value . '</small>';
            }
            elseif ($name == 'array' || $name == 'object')
            {
                $value = preg_replace('/(\[|\]|array|object)/', '<b>$0</b>', $value);
            }
            return '<span  style="color:#' . $this->colors[$name][0] . '">' . $value . '</span>';
        }
        else
        {
            return $this->format($value, $this->colors[$name][1]);
        }
    }

    /**
     * Format the size of array elements or length of string.
     *
     * @param int $size
     * @param int $type
     * @return string
     */
    private function counter(int $size, int $type = 0): string
    {
        return $this->color('(' . ($type ? 'length' : 'size')  . '=' . $size . ')', 'size');
    }

    /**
     * Formats the data type.
     *
     * @param string $type
     * @param string $before
     * @return string
     */
    private function type(string $type, string $before = ' '): string
    {
        return $before . $this->color($type, 'type');
    }

    /**
     * Move cursor to next line.
     *
     * @return string
     */
    private function breakLine(): string
    {
        return $this->isCli ? PHP_EOL : '<br />';
    }

    /**
     * Indents line content.
     *
     * @param int $pad
     * @return string
     */
    private function indent(int $pad): string
    {
        return str_repeat((! $this->isCli) ? '&nbsp;' : ' ', $pad);
    }

    /**
     * Adds padding to the line.
     *
     * @param int $size
     * @return string
     */
    private function pad(int $size): string
    {
        return str_repeat((! $this->isCli) ? '&nbsp;' : ' ', $size < 0 ? 0 : $size);
    }

    /**
     * Formats array index.
     *
     * @param $key
     * @param bool $parent
     * @return string
     */
    private function arrayIndex(string $key, bool $parent = false): string
    {
        if (!$parent)
        {
            return $this->color("'$key'", 'multi_array_key') . ' ' . $this->color('=', 'multi_array_arrow') . ' ';
        }
        else
        {
            return $this->color("'$key'", 'single_array_key') . ' ' . $this->color('=>', 'single_array_arrow') . ' ';
        }
    }

    /**
     * Formats array elements.
     *
     * @param array $array
     * @param bool $obj_call
     * @return string
     */
    private function formatArray(array $array, bool $obj_call): string
    {
        $tmp = '';
        $this->indent += $this->pad_size;
        foreach ($array as $key => $arr)
        {
            if (is_array($arr))
            {
                $tmp .= $this->breakLine() . $this->indent($this->indent) . $this->arrayIndex((string) $key) . ' ' . $this->counter(count($arr));
                $new = $this->formatArray($arr, $obj_call);
                $tmp .=  ($new != '') ? " {{$new}{$this->indent($this->indent)}}" : ' {}';
            }
            else
            {
                $tmp .= $this->breakLine() . $this->indent($this->indent) . $this->arrayIndex((string) $key, true)
                    . $this->evaluate([$arr], true);
            }
        }
        $this->indent -= $this->pad_size;
        if ($tmp != '')
        {
            $tmp .= $this->breakLine();
            if ($obj_call)
            {
                $tmp .= $this->indent($this->indent);
            }
        }

        return $tmp;
    }

    /**
     * Gets the id of an object. (DIRTY)
     *
     * @param $object
     * @return string
     */
    private function refcount($object): string
    {
        ob_start();
        debug_zval_dump($object);
        if (preg_match('/object\(.*?\)#(\d+)\s+\(/', ob_get_clean(), $match))
        {
            return $match[1];
        }
    }

    /**
     * Formats object elements.
     *
     * @param $object
     * @return mixed|null|string
     */
    private function formatObject($object)
    {
        if ($this->aboveNestLevel())
        {
            return $this->color('...', 'recursion');
        }

        $reflection = new ReflectionObject($object);
        $tmp = '';
        $this->indent += $this->pad_size;
        foreach ($reflection->getProperties() as $size => $prop)
        {
            if ($prop->isPrivate())
            {
                $tmp .= $this->breakLine() . $this->indent($this->indent) . $this->color('private', 'property_visibility')
                    . $this->pad(2) . ' ' . $this->color(':', 'property_arrow') . ' ';
            }
            elseif ($prop->isProtected())
            {
                $tmp .= $this->breakLine() . $this->indent($this->indent) . $this->color('protected', 'property_visibility') . ' '
                    . $this->color(':', 'property_arrow') . ' ';
            }
            elseif ($prop->isPublic())
            {
                $tmp .= $this->breakLine() . $this->indent($this->indent) . $this->color('public', 'property_visibility')
                    . $this->pad(3) . ' ' . $this->color(':', 'property_arrow') . ' ';
            }

            $prop->setAccessible(true);
            $tmp .= $this->color('\'' . $prop->getName() . '\'', 'property_name') . ' '
                . $this->color('=>', 'property_arrow') . ' '
                . $this->evaluate([$prop->getValue($object)], true, true);
        }

        if ($tmp != '')
        {
            $tmp .= $this->breakLine();
        }

        $this->indent -= $this->pad_size;
        $tmp .= ($tmp != '') ? $this->indent($this->indent) : '';

        $tmp =  str_replace([':name', ':id', ':content'], [
            $reflection->getName(),
            $this->color('#' . $this->refcount($object), 'size'),
            $tmp
        ], $this->color('object (:name) [:id] [:content]', 'object'));


        return $tmp;
    }

    /**
     * Couples all formats.
     *
     * @param array $args
     * @param bool $called
     * @param bool $from_obj
     * @return string
     */
    private function evaluate(array $args, bool $called = false, bool $from_obj = false): string
    {
        $tmp = null;
        foreach ($args as $each)
        {
            $type = gettype($each);
            switch ($type)
            {
                case 'string':
                    $tmp .=  $this->color('\'' . $each . '\'', $type) . ' ' . $this->counter(strlen($each), 1) . $this->type($type);
                    break;
                case 'integer':
                    $tmp .=  $this->color((string) $each, $type) . $this->type($type);
                    break;
                case 'double':
                    $tmp .= $this->color((string) $each, $type) . $this->type($type);
                    break;
                case 'NULL':
                    $tmp .= $this->color('null', 'null') . $this->type($type);
                    break;
                case 'boolean':
                    $tmp .= $this->color($each ? 'true' : 'false', $type) . $this->type($type);
                    break;
                case 'array':
                    $tmp .= str_replace([':size', ':content'], [
                        $this->counter(count($each)),
                        $this->formatArray($each, $from_obj)
                    ], $this->color('array :size [:content]', 'array'));
                    break;
                case 'object':
                    $tmp .= $this->formatObject($each);
                    break;
            }

            if (!$called)
            {
                $tmp .= $this->breakLine();
            }

        }

        return $tmp;
    }

}
