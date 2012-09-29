# XML for LaravelPHP #

This probably isn't the greatest XML library in the world, but it gets the job done for me.

## Usage ##

```
// get from file
$array = XML::from_file($path)->array;

// get from string
$array = XML::from_string($xml)->array;

// get a value from xml array
$value = XML::from_file($path)->get('foo.bar.value'); // dot-walking the array
```

## Future Plans ##

* I need to have a ``to_file()`` method for making physical XML files from arrays.