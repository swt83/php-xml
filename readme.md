# XML

This probably isn't the greatest XML library in the world, but it gets the job done for me.  The class converts the XML into an array for easy use.

## Usage

```php
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

// save to file
$success = XML::from_array($array)->to_file('root_node_name', $path);
```

The code that converts arrays to XML files is written by [Lalit Patel](http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes/).

```php
$books = 1984;  // or
$books = array(
    '@value' = 1984
);
// creates <books>1984</books>

$books = array(
    '@attributes' => array(
        'type' => 'fiction'
    ),
    '@value' = 1984
);
// creates <books type="fiction">1984</books>

$books = array(
    '@attributes' => array(
        'type' => 'fiction'
    ),
    'book' => 1984
);
/* creates
<books type="fiction">
  <book>1984</book>
</books>
*/

$books = array(
    '@attributes' => array(
        'type' => 'fiction'
    ),
    'book'=> array('1984','Foundation','Stranger in a Strange Land')
);
/* creates
<books type="fiction">
  <book>1984</book>
  <book>Foundation</book>
  <book>Stranger in a Strange Land</book>
</books>
*/

XML::from_array($books)->to_file('root', $path);
```