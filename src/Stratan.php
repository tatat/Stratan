<?php

class Stratan implements IteratorAggregate, ArrayAccess, Countable {
  protected $data = null;

  protected $separator = '.';

  protected $json_options = 0;

  public function __construct(&$array = array(), $json_options = 0) {
    if ($array instanceof Stratan) {
      $this->data =& $array->data();
    } else {
      $this->data =& $array;
    }
    
    $this->json_options = $json_options;
  }

  public function offsetExists($index) {
    return $this->has_key($index);
  }

  public function offsetGet($index) {
    return $this->get($index);
  }

  public function offsetSet($index, $value) {
    $this->set($index, $value);
  }

  public function offsetUnset($index) {
    $this->delete($index);
  }

  public function getIterator() {
    return new ArrayIterator($this->data);
  }

  public function count() {
    return count($this->data);
  }

  public function set_separator($separator) {
    $this->separator = $separator;
    return $this;
  }

  public function keys() {
    return array_keys($this->data);
  }

  public function values() {
    return array_values($this->data);
  }

  public function get($key, $default = null) {
    $parent =& $this->get_parent_array($key, $last_ns);

    if (!is_null($parent) && array_key_exists($last_ns, $parent)) {
      $class = __CLASS__;
      return is_array($parent[$last_ns]) ? new $class($parent[$last_ns], $this->json_options) : $parent[$last_ns];
    } else {
      return $default;
    }
  }

  public function has_key() {
    foreach (func_get_args() as $value) {
      $parent = $this->get_parent_array($value, $last_ns);
      if (is_null($parent) || !array_key_exists($last_ns, $parent))
        return false;
    }

    return true;
  }

  public function set($key, $value = null) {
    $class = __CLASS__;

    if (is_null($key)) {
      $this->data[] = $value;
    } else if (is_array($key) || $key instanceof $class) {
      foreach ($key as $k => $v)
        $this->data[$k] = $v;
    } else {
      $parent =& $this->get_parent_array($key, $last_ns, true);
      $parent[$last_ns] = $value;
    }

    return $this;
  }

  public function set_default($key, $value) {
    $parent =& $this->get_parent_array($key, $last_ns, true);

    if (!array_key_exists($last_ns, $parent))
      $parent[$last_ns] = $value;

    return $this;
  }

  public function set_defaults(array $defaults) {
    foreach ($defaults as $key => $value)
      $this->set_default($key, $value);

    return $this;
  }

  public function delete($key) {
    $parent =& $this->get_parent_array($key, $last_ns);

    if (!is_null($parent) && array_key_exists($last_ns, $parent))
      unset($parent[$last_ns]);

    return $this;
  }

  public function &data() {
    return $this->data;
  }

  public function to_array() {
    return $this->create_array_copy($this->data);
  }

  public function to_object() {
    return $this->create_array_copy($this->data, true);
  }

  public function to_json() {
    return json_encode($this->data, $this->json_options);
  }

  public function __get($name) {
    if (preg_match('/^(array|object|json)_value$/', $name, $matches)) {
      switch ($matches[1]) {
        case 'array': return $this->create_array_copy($this->data);
        case 'object': return $this->create_array_copy($this->data, true);
        case 'json': return json_encode($this->data, $this->json_options);
      }
    } else {
        return $this->get($name);
    }
  }

  public function __set($name, $value) {
    $this->data[$name] = $value;
  }

  protected function &get_parent_array($key, &$last_ns, $create_if_not_exist = false) {
    $current =& $this->data;

    $ns = explode($this->separator, $key);
    $last_ns = array_pop($ns);

    foreach ($ns as $k) {
      if (!array_key_exists($k, $current) || !is_array($current[$k])) {
        if ($create_if_not_exist) {
          $current[$k] = array();
        } else {
          $null = null;
          return $null;
        }
      }

      $current =& $current[$k];
    }

    return $current;
  }

  protected function create_array_copy($data, $to_object = false) {
    $array = array();

    foreach ($data as $key => $value)
      $array[$key] = is_array($value) ? $this->create_array_copy($value, $to_object) : $value;

    return $to_object ? (object) $array : $array;
  }
}