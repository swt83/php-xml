<?php

/**
 * A LaravelPHP package for working w/ XML files.
 *
 * @package    XML
 * @author     Scott Travis <scott.w.travis@gmail.com>
 * @link       http://github.com/swt83/laravel-xml
 * @license    MIT License
 */

class XML
{
    public $array;

    /**
     * Build object from path.
     *
     * @param   string  $path   Location of physical XML file
     * @return  object
     */
    public static function from_file($path)
    {
        // open file
        $string = file_get_contents($path);

        // catch error...
        if ($string)
        {
            // build object
            return static::from_string($string);
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Build object from URL (string).
     *
     * @param   string  Input URL from which to grab XML
     * @return  object
     */
    public static function from_url($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch))
        {
            #$errors = curl_error($ch);
            curl_close($ch);
            return false;
        }
        else
        {
            curl_close($ch);
        
            if ($code === 404)
            {
                return false;
            }
            else
            {
                return static::from_string($result);
            }
        }
    }
    
    /**
     * Build object from string.
     *
     * @param   $string Input XML as string
     * @return  object
     */
    public static function from_string($string)
    {
        $class = __CLASS__;
        return new $class($string);
    }
    
    /**
     * Constructor class.
     *
     * @param   $string Input of XML as string
     * @return  object
     */
    public function __construct($string)
    {
        // The following is a function I've had for a long time,
        // and I have no idea where I got it.  It takes an XML string,
        // parses it, and converts it to an array.  We'll store that
        // array in the object and use it as we need.
        
        /////////////////////////////////////////////////
        
        if (!function_exists('xml_parser_create')) return false;
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $string, $dataml_values);
        xml_parser_free($parser);
        if (!$dataml_values) return;
        $dataml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();
        $current = &$dataml_array;
        foreach($dataml_values as $data)
        {
            unset($attributes,$value);
            extract($data);
            $result = '';
            $result = array();
            if (isset($value)) $result['value'] = $value;
            if (isset($attributes))
            {
                foreach($attributes as $attr => $val)
                {
                    $result['attr'][$attr] = $val;
                }
            }
            if ($type == 'open')
            {
                $parent[$level-1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current))))
                {
                    $current[$tag] = $result;
                    $current = &$current[$tag];
                }
                else 
                {
                    if (isset($current[$tag][0]))
                    {
                        array_push($current[$tag], $result);
                    }
                    else
                    {
                        $current[$tag] = array($current[$tag],$result);
                    }
                    $last = count($current[$tag]) - 1;
                    $current = &$current[$tag][$last];
                }
            }
            elseif ($type == 'complete')
            {
                if (!isset($current[$tag]))
                {
                    $current[$tag] = $result;
                }
                else
                {
                    if (is_array($current[$tag]) or (isset($current[$tag][0]) and is_array($current[$tag][0])))
                    {
                        array_push($current[$tag],$result);
                    }
                    else
                    {
                        $current[$tag] = array($current[$tag],$result);
                    }
                }
            }
            elseif ($type == 'close')
            {
                $current = &$parent[$level-1];
            }
        }
        
        /////////////////////////////////////////////////
        
        // save object array
        $this->array = $dataml_array;
    }
    
    /**
     * Get specific value from object array.
     *
     * @param   string  $dots   Dot separated array coordinates
     * @param   string  $default    Default value if coordinates not found
     * @return  mixed
     */
    public function get($dots, $default = null)
    {
        $keys = explode('.', $dots);
        $value = $this->array;
        foreach ($keys as $key)
        {
            if (isset($value[$key]))
            {
                $value = $value[$key];
            }
            else
            {
                return $default;    
            }
        }
        return $value;
    }

    /**
     * Get object array as array.
     *
     * @return  array
     */
    public function to_array()
    {
        return $this->array;
    }
}