# XML

A PHP library for working w/ XML files.

## Usage

When loading XML files:

```php
// get from url
$object = Travis\XML::from_url($url);

// get from file
$object = Travis\XML::from_file($path);

// get from string
$object = Travis\XML::from_string($xml);

// get a value from xml array
$value = Travis\XML::from_file($path)->get('foo.bar.value'); // dot-walking the array

// get entire array
$array = Travis\XML::from_file($path)->to_array();

// save to file
$success = Travis\XML::from_array($array)->to_file('root_node_name', $path);
```

When building new XML files:

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

Travis\XML::from_array($books)->to_file('root', $path);
```

The code that converts arrays to XML files is written by [Lalit Patel](http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes/).  This package is just a handy wrapper.