# XML for LaravelPHP #

This probably isn't the greatest XML library in the world, but it get's the job done for me.

## Usage ##

When working w/ XML you always want to 

```
// get from file
$array = XML::from_file($path)->array;

// get from string
$array = XML::from_string($xml)->array;

// get a value from array
$xml = XML::from_file($path);
$value = $xml->get('foo.bar.value'); // dot-walk the array
```

## Future Plans ##

* I need to have a ``to_file()`` method for making XML files.