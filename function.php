<?php

class Dump
{
    const NAME      = '#f07b06';//default orrange
    const VALUE     = '#0000ff';//default blue
    const DATA_N    = '#bbbbbb';//default gray
    const DATA_TY   = '#5ba415';//default lemon
    const N_ARRAY   = '#000000';//default black
    const D_ARRAY   = '#b735e7';//default light purple
    const D_NULL    = '#6789f8';//default light blue
    const FLOT      = '#9c6e25';//default brown
    const PNT       = '#f00000';//default red
    const NPNT      = '#e103c4';//default pink
    const INTE      = '#1baabb';//default greenishblue
    const A_PT      = '#59829e';//default light navy blue
    const A_CT      = '#9d4451';//default light maroon
    
    private $marg = 20;
    private $arr_count = null;
    private $detem_last = 1;
    private $proc_end = false;
    
    public function __construct()
    {
        echo $this->dump(func_get_args());
    }
    private function dump()
    {
        $args = func_get_args();
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
                $dumped .= '<code><span style="color:purple;">';
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
                    $dumped .= '(size=' . $length . ')</i> [ </code><br />';
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
                        $dumped .= '<code>]<br /></code>';
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
        }
         return $dumped;
    }
}