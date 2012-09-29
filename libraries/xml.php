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

	public static function from_file($path)
	{
		// open file
		$string = file_get_contents($path);
		
		// if file extraction successful...
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
	
	public static function from_string($string)
	{
		$class = __CLASS__;
		return new $class($string);
	}
	
	public function __construct($string)
	{
		// a function from a long, long time ago...
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
		$this->array = $dataml_array;
	}
	
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
			else return $default;
		}
		return $value;
	}
}