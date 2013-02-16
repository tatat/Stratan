Stratan
====================

Array Manipulator.

Feature
--------------------

### Indifferent access

```php
$object = Stratan::create(array(
  'default' => array(
    'value1' => array(
      'is' => 'set'
    ),
    'value2' => array(
      'is' => 'set'
    ),
    'value3' => array(
      'is' => 'set'
    )
  )
));

var_dump($object->get('default.value1.is'));
var_dump($object['default.value2.is']);
var_dump($object->default->value3->is);

$object->set('default.value1.is', 'changed');
$object['default.value2.is'] = 'changed';
$object->default->value3->is = 'changed';

var_dump($object->get('default.value1.is'));
var_dump($object['default.value2.is']);
var_dump($object->default->value3->is);
```

output

```
string(3) "set"
string(3) "set"
string(3) "set"
string(7) "changed"
string(7) "changed"
string(7) "changed"
```

API
--------------------

### Static methods

#### create($data) -> Stratan

* `$data`: array or instance

```php
$object = Stratan::create(array(
  'default.value 1.is' => 'set',
  'default.value 2.is.also' => 'set'
));

var_export($object->to_array());
```
output

```php
array (
  'default' =>
  array (
    'value 1' =>
    array (
      'is' => 'set'
    ),
    'value 1' =>
    array (
      'is' =>
      array (
        'also' => 'set'
      )
    )
  )
)
```

#### create_array($data) -> array

* `$data`: array or instance

```php
$array = Stratan::create_array(array(
  'default.value 1.is' => 'set',
  'default.value 2.is.also' => 'set'
));

var_export($array);
```

#### create_object($data) -> stdObject

* `$data`: array or instance

#### create_json($data) -> string

* `$data`: array or instance

### Instance methods

#### __construct(&$data, [$separator = null], [$json\_options = 0]) -> $this

* `&$data`: array or instance
* `$separator`: string
* `$json_options`: integer

```php
$array = array(
  'this.is' => 'set'
);

$object = new Stratan($array);
$object->set('that.is', 'set');

var_export($array); // not $object
```

output

```php
array (
  'this' =>
  array(
    'is' => 'set'
  ),
  'that' =>
  array (
    'is' => 'set'
  )
)
```

#### getIterator() -> ArrayIterator

#### count() -> integer

#### set_separator($separator) -> $this

* `$separator`: string

#### keys() -> array

#### values() -> array

#### offsetExists($key) -> boolean
#### has_key($key) -> boolean

* `$key`: string

#### offsetGet($key) -> mixed
#### get($key, [$default\_value]) -> mixed

* `$key`: string
* `$default_value`: mixed

#### offsetSet($key, $value) -> $this
#### set($key, $value) -> $this

* `$key`: string
* `$value`: mixed

#### set($value) -> $this

* `$value`: array or instance

#### set_default($key, $value) -> $this

* `$key`: string
* `$value`: mixed

#### set_default($value) -> $this

* `$value`: array or instance

#### offsetUnset($key) -> $this
#### delete($key) -> $this

* `$key`: string

#### merge($data) -> $this

* `$data`: array

#### flatten() -> array

```php
$object = Stratan::create(array(
  'default' => array(
    'value 1' => array(
      'is' => 'set'
    ),
    'value 2' => array(
      'is' => array(
        'also' => 'set'
      )
    )
  )
));

var_export($object->flatten());
```

output

```php
array(
  'default.value 1.is' => 'set',
  'default.value 2.is.also' => 'set'
)
```

#### &data() -> array

#### to_array() -> array

#### to_object() -> stdObject

#### to_json() -> string

Licence
--------------------

The MIT License (MIT)

Copyright (c) 2013 tatあt

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.