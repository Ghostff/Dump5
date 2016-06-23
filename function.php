<?php

class Dump
{
    const NAME      = '#f07b06';//default orrange
    const VALUE     = '#0000ff';//default blue (for string value)
    const DATA_N    = '#bbbbbb';//default gray (for 'string')
    const DATA_TY   = '#5ba415';//default lemon (for lenght or size)
    const N_ARRAY   = '#000000';//default black (for array or objects)
    const BOOL      = '#bb02ff';//default light purple (for bool)
    const D_NULL    = '#6789f8';//default light blue (for null)
    const FLOT      = '#9c6e25';//default brown (for float)
    const PNT       = '#f00000';//default red (for refrences like '=>' and ':')
    const NPNT      = '#e103c4';//default pink (for '=')
    const INTE      = '#1baabb';//default greenishblue (for int)
    const A_PT      = '#59829e';//default light navy blue (for array key)
    const VISIB     = '#741515';//default dark red (for object visibility)
    const VAR_N     = '#987a00';//default light brown (for object variable name)
    
    private $marg = 20;
    private $arr_count = null;
    private $detem_last = 1;
    private $proc_end = false;
    private $instance = true;
    
    public function __construct()
    {
        echo $this->dump(func_get_args());
    }
    private function objects($object)
    {
        $vals = array();
        $obj = new ReflectionObject($object);
        $vals['class'] = $obj->getName();
        foreach ($obj->getProperties() as $key =>  $prop) {
			//the &nbsp; is to make sure visibilities are aligned
            if ($prop->isPrivate()) {
                $vals[$key]['visibility'] = 'private&nbsp;&nbsp;';
            }
            elseif ($prop->isProtected()) {
                $vals[$key]['visibility'] = 'protected';
            }
            elseif ($prop->isPublic()) {
                $vals[$key]['visibility'] = 'public&nbsp;&nbsp;&nbsp;';    
            }
            $getValue = $obj->getDefaultProperties();
            $vals[$key]['name'] = $prop->getName();
            $vals[$key]['value'] = $getValue[$prop->getName()];
        }
        return $vals;
    }
    private function dump()
    {
        $args = func_get_args();
        if ($this->instance) {
            $args = $args[0];
            $this->instance = false;
        }
        $dumped = '';
        for ($i = 0; $i < count($args); $i++) 
        {
            $data_type = gettype($args[$i]);
            if ($data_type == 'string') {
                $length = strlen($args[$i]);
                $dumped .= '<code><span style="color:' . self::VALUE . ';">\'' . $args[$i] . '\'</span> <i style="color:' .self::DATA_TY;
                $dumped .= ';">(length=' . $length . ')</i> <small style="color:' . self::DATA_N . ';"> string</small></code><br />';
            }
            elseif ($data_type == 'integer') {
                $dumped .= '<code><span style="color:' . self::INTE . ';">' . $args[$i] . '</span>';
                $dumped .= ' <small style="color:' . self::DATA_N . ';"> int</small></code><br />';
            }
            elseif ($data_type == 'double') {
                $dumped .= '<code><span style="color:' . self::FLOT . ';">' . $args[$i] . '</span>';
                $dumped .= '<small style="color:' . self::DATA_N . ';"> float</small></code><br />';
            }
            elseif ($data_type == 'boolean') {
                $dumped .= '<code><span style="color:' . self::BOOL . ';">';
                $dumped .= ($args[$i])? 'true</span>':'false</span>';
                $dumped .= '<small style="color:' . self::DATA_N . ';"> boolean</small></code><br />';
            }
            elseif ($data_type == 'NULL') {
                $dumped .= '<code><span style="color:' . self::D_NULL . ';">null</span></code><br />';
            }
            elseif ($data_type == 'array') {
                $length = count($args[$i]);
                if (!$this->arr_count) {
                    $this->arr_count = count($args[$i], COUNT_RECURSIVE);
                }
                if (!$this->proc_end && $this->marg == 20) {
                    $dumped .= '<code><b style="color:' . self::N_ARRAY . ';">array</b> <i style="color:' .self::DATA_TY . ';">';
                    $dumped .= '(size=' . $length . ')</i> [<br />';
                    if ($length == 0) {
                        $this->marg += 20;
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;">(empty)</code>';
                        $this->marg -= 20;
                        $dumped .= '<br /><code style="margin-left:' .$this->marg. 'px;">]</code> <br />';
                    }
                }
                foreach ($args[$i] as $key => $values) {
                    if (is_array($values)) {
                        $this->marg += 20;
                        $length = count($values);
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;">';
                        $dumped .= '<span style="color:'. self::A_PT . '">\'' . $key . '\'</span>';
                        $dumped .=  '</span> <span style="color:'. self::NPNT . '">=</span> ';
                        $dumped .= ' <b style="color:'. self::N_ARRAY .';">array</b>';
                        $dumped .= ' <i style="color:' .self::DATA_TY . ';">(size = ' . $length . ')</i> { </code><br />';
                        $dumped .= $this->dump($values);
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;">}</code> <br />';
                        $this->marg -= 20;
                    }
                    else{
                        $this->marg += 20;
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;"><span style="color:'. self::NAME . '">\'' . $key;
                        $dumped .= '\'</span> </span> <span style="color:'. self::PNT . '">=></span> </code>' . $this->dump($values);
                        $this->marg -= 20;
                    }
                    if ($this->marg == 20 && $this->arr_count == $this->detem_last) {
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;">]<br /></code>';
                        $this->proc_end = false;
                        $this->arr_count = null;
                        $this->detem_last = 1;
                    }
                    else {
                        $this->proc_end = true;
                        $this->detem_last++;
                    }
                }
            }
            elseif ($data_type == 'object') {
                $object = $this->objects($args[$i]);
                $dumped .= '<code><b style="color:' . self::N_ARRAY . ';">object</b> <i style="color:' .self::DATA_TY . ';">';
                $dumped .= '(' . $object['class'] . ')</i><br />';
                foreach ($object as $key => $values) {
                    if (is_array($values)) {
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;">';
                        $dumped .= '<span style="color:'. self::VISIB . '">' . $values['visibility'] . '</span>';
                        $dumped .= '</span> <span style="color:'. self::VAR_N . '">';
                        if (is_array($values['value'])) {
                            $dumped .= '\'' . $values['name'] . '\' </span>';
                            $dumped .= '<span style="color:'. self::PNT . '"> : </span>';
                            $dumped .= $this->dump($values['value']);
                        }
                        else {
                            $dumped .= '\'' . $values['name'] . '\' </span>';
                            $dumped .= '<span style="color:'. self::PNT . '"> : </span>';
                            $dumped .= $this->dump($values['value']);
                        }
                    }
                }
            }
        }
         return $dumped;
    }
    

}