# XML for LaravelPHP #

This probably isn't the greatest XML library in the world, but it gets the job done for me.  The class converts the XML into an array for easy use.

## Usage ##

```
// get from url
$object = XML::from_url($url);

// get from file
$object = XML::from_file($path);

// get from string
$object = XML::from_string($xml);

// get a value from xml array
$value = XML::from_file($path)->get('foo.bar.value'); // dot-walking the array

// get entire array
$array = XML::from_file($path)->to_array();
```

## Future Plans ##

* I need to have a ``to_file()`` method for making physical XML files from arrays.