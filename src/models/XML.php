<?php

namespace Travis;

class XML
{
    /**
     * Core array of data.
     *
     * @var     array
     */
    public $array;

    /**
     * Build object from string.
     *
     * @param   string  $string
     * @return  object
     */
    public static function fromString($string)
    {
        $class = __CLASS__;
        $object = new $class;
        $object->convert($string);
        return $object;
    }

    /**
     * Build object from path.
     *
     * @param   string  $path
     * @return  object
     */
    public static function fromFile($path)
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
     * @param   string  $url
     * @return  object
     */
    public static function fromURL($url)
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
            if ($code == 404)
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
     * Build object from array.
     *
     * @param   array   $array
     * @return  object
     */
    public static function fromArray($array)
    {
        if (!is_array($array)) trigger_error('Must be array.');

        $class = __CLASS__;
        $object = new $class;
        $object->array = $array;
        return $object;
    }

    /**
     * Convert string to array.
     *
     * @param   string  $string
     * @return  void
     */
    protected function convert($string)
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
     * @param   string  $dots
     * @param   string  $default
     * @return  mixed
     */
    public function get($dots, $default = null)
    {
        return ex($this->array, $dots, $default);
    }

    /**
     * Return object array.
     *
     * @return  array
     */
    public function toArray()
    {
        return $this->array;
    }

    /**
     * Return XML as string.
     *
     * @param   string  $root_node_name
     * @return  boolean
     */
    public function toFile($root_node_name = 'root')
    {
        // build
        $xml = \Array2XML::createXML($root_node_name, $this->array);

        // convert
        return $xml->saveXML();
    }

    /**
     * Save object array as file.
     *
     * @param   string  $path
     * @param   string  $root_node_name
     * @return  boolean
     */
    public function toFile($path, $root_node_name = 'root')
    {
        // save
        return file_put_contents($path, $this->toString($root_node_name));
    }
}