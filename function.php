<?php
class Controls
{
    const NAME      = '#f07b06';//default orrange
    const VALUE     = '#0000ff';//default blue
    const DATA_N    = '#bbbbbb';//default gray
    const DATA_TY   = '#5ba415';//default lemon
    const N_ARRAY   = '#000000';//default black
    const D_ARRAY   = '#b735e7';//default light purple
    const D_NULL    = '#6789f8';//default light blue
    const FLOT      = '#9c6e25';//default brown
    const INTE      = '#1baabb';//default brown
    
    private $marg = 20;
    public function dump()
    {
        $numArgs = func_num_args();
        $args = func_get_args();
        $dumped = '';
        for ($i = 0; $i < $numArgs; $i++) {
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
                if (empty($dumped) && $this->marg == 20) {
                    $dumped = '<code><b style="color:'. self::N_ARRAY .';">array</b> <i style="color:' .self::DATA_TY . ';">';
                    $dumped .= '(size=' . $length . ')</i> { </code><br />';
                }
                foreach ($args[$i] as $key => $values) {
                    if (is_array($values)) {
                        $this->marg += 10;
                        $length = count($values);
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;"><span style="color:'. self::D_ARRAY .';">\'' .$key. '\'';
                        $dumped .= '</span> <b style="color:'. self::N_ARRAY .';">array</b> <i style="color:' .self::DATA_TY . ';">';
                        $dumped .= '(size=' . $length . ')</i>{<br /><code style="margin-left:';
                        $dumped .= $this->marg. 'px;"></code>'. $this->dump($values);
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;">}</code><br />';
                        $this->loop_is_on = true;
                        $this->marg -= 10;
                    }
                    else{
                        $dumped .= '<code style="margin-left:' .$this->marg. 'px;"><span style="color:'. self::NAME . '">\'' .$key;
                        $dumped .= '\'</span> <span style="color:red;">=></span> </code> ' . $this->dump($values);
                    }
                }
            }
        }
         return $dumped;
    }
}