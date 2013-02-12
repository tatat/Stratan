<?php

class Stratan implements IteratorAggregate, ArrayAccess, Countable {
  static public function create($array = array(), $separator = null, $json_options = 0) {
    $empty = array();

    $object = new static($empty, $separator, $json_options);
    unset($empty);

    $object->set($array);

    return $object;
  }

  static public function __callStatic($name, $args) {
    if (preg_match('/^create_(array|object|json)$/', $name, $matches)) {
      return call_user_func_array(array(__CLASS__, 'create'), $args)->{"to_{$matches[1]}"}();
    } else {
      trigger_error('Call to undefined method ' . __CLASS__ . "::{$name}()", E_USER_ERROR);
    }
  }

  protected $data = null;

  protected $separator = '.';

  protected $json_options = 0;

  public function __construct(&$array = array(), $separator = null, $json_options = 0) {
    if ($array instanceof Stratan) {
      $this->data =& $array->data();
    } else {
      $this->data =& $array;
    }

    if (!is_null($separator))
      $this->separator = $separator;
    
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
      return is_array($parent[$last_ns]) ? new static($parent[$last_ns], $this->separator, $this->json_options) : $parent[$last_ns];
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

  public function set($key, $value = null, $if_not_exist = false) {
    return $this->_set($key, $value, $if_not_exist);
  }

  public function set_default($key, $value = null) {
    return $this->set($key, $value, true);
  }

  public function delete($key) {
    $parent =& $this->get_parent_array($key, $last_ns);

    if (!is_null($parent) && array_key_exists($last_ns, $parent))
      unset($parent[$last_ns]);

    return $this;
  }

  public function merge($value) {
    $this->_merge($this->data, $value);
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

  protected function _set($key, $value = null, $if_not_exist = false, $prefix = null) {
    if (is_null($key)) {
      if (is_array($value)) {
        $this->data[] = static::create_array($value);
      } else if ($value instanceof Stratan) {
        $this->data[] = $value->to_array();
      } else {
        $this->data[] = $value;
      }
    } else if (is_array($key)) {
      foreach ($key as $k => $v)
        $this->_set($k, $v, $if_not_exist, $prefix);
    } else if ($key instanceof Stratan) {
      $this->merge($key->to_array());
    } else {
      if (!is_null($prefix))
        $key = $prefix . $this->separator . $key;

      if (is_array($value) && count($value) > 0) {
        $this->_set($value, null, $if_not_exist, $key);
      } else if ($value instanceof Stratan) {
        $this->merge($value->to_array());
      } else {
        $parent =& $this->get_parent_array($key, $last_ns, true);

        if ($if_not_exist && array_key_exists($last_ns, $parent))
          return $this;

        $parent[$last_ns] = $value;
      }
    }

    return $this;
  }

  protected function _merge(&$source, $item) {
    foreach ($item as $key => $value) {
      if (is_array($item[$key])) {
        if (!array_key_exists($key, $source) || !is_array($source[$key]))
          $source[$key] = array();

        $this->_merge($source[$key], $item[$key]);
      } else {
        $source[$key] = $item[$key];
      }
    }
  }
}