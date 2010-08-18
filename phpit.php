<?php

function __lambda($value) {
  return $value;
};

function __def($param, $default = null) {
  return !!$param ? $param : $default;
}

class RClass {
  protected $__;

  public function __toString() {
    return $this->__;
  }
}

// Ruby like Array class
class RArray extends ArrayObject {
  public function __construct($array = array()) {
    parent::__construct($array);
  }

  public function __toString() {
    return $this->visualize();
  }

  public function visualize() {
    // return json_encode((array)$this->native());

    $output = array();
    foreach ($this->native() as $key => $value) {
      if (is_array($value))
        $output[] = "array(" . join(", ", $value) . ")";
      if (is_string($value))
        $output[] = "\"$value\"";
      else
        $output[] = $value;
    }
    return "array(" . join(", ", $output) . ")";
  }

  public function inspect() { return $this->visualize(); }

  public function get($index) {
    return $this[$index];
  }

  public function native() {
    $array = array();
    foreach ($this as $key => $value) {
      if ($value instanceof RArray || $value instanceof RString || $value instanceof RInt)
        $array[$key] = $value->native();
      else
        $array[$key] = $value;
    }
    return $array;
  }

  // basic array functions
  public function size() { return $this->count(); }
  public function length() { return $this->count(); }

  public function push($element) {
    $this->append($element);
    return $this;
  }

  public function pop($element) {
    $this->append($element);
    return $this;
  }

  public function slice($begin, $length = null) {
    return new RArray(array_slice((array)$this, $begin, $length));
  }

  public function each($iterator) {
    foreach ($this as $key => $value) {
      if ($iterator($value, $key) === false)
        break;
    }
    return $this;
  }

  public function eachSlice($number, $iterator = null) {
    $iterator = __def($iterator, '__lambda');
    $index = -$number; $slices = new RArray; $array = $this;
    if ($number < 1) return $array;
    while (($index += $number) < $array->length())
      $slices->push($array->slice($index, $number));
    return $slices->collect($iterator);
  }

  public function first() {
    return $this[0];
  }

  public function last() {
    return $this[$this->length() - 1];
  }

  public function compact() {
    return $this->select(function($value) {
      return $value != null;
    });
  }

  public function all($iterator = null) {
    $iterator = __def($iterator, '__lambda');
    $result = true;
    foreach ($this as $index => $value) {
      $result = $result && !!$iterator($value, $index);
      if (!$result) break;
    }
    return $result;
  }

  public function any($iterator = null) {
    $iterator = __def($iterator, '__lambda');
    $result = false;
    foreach ($this as $index => $value) {
      if ($result = !!$iterator($value, $index))
        break;
    }
    return $result;
  }

  public function collect($iterator = null) {
    $iterator = __def($iterator, '__lambda');
    $results = new RArray;
    foreach ($this as $index => $value) {
      $results->push($iterator($value, $index));
    }
    return $results;
  }

  public function map($iterator = null) { return $this->collect($iterator); }

  public function detect($iterator) {
    foreach ($this as $index => $value) {
      if ($iterator($value, $index))
        return $value;
    }
    return null;
  }

  public function find($iterator) { return $this->detect($iterator); }

  public function select($iterator) {
    $results = new RArray;
    foreach ($this as $index => $value) {
      if ($iterator($value, $index))
        $results->push($value);
    }
    return $results;
  }

  public function findAll($iterator) { return $this->select($iterator); }

  public function filter($iterator) { return $this->select($iterator); }

  public function grep($filter, $iterator = null) {
    $iterator = __def($iterator, '__lambda');
    return $this->select(function($value, $index) use($filter, $iterator, &$results) {
      return !!preg_match($filter, $value);
    });
  }

  public function has($object) {
    // use in_array()
    // return in_array($object, (array)$this);
    foreach ($this as $index => $value) {
      if ($value == $object)
        return true;
    }
    return false;
  }

  public function inject($memo, $iterator) {
    foreach ($this as $index => $value) {
      $memo = $iterator($memo, $value, $index);
    }
    return $memo;
  }

  public function join($glue) {
    return join($glue, (array)$this);
  }

  public function max($iterator = null) {
    $iterator = __def($iterator, '__lambda');
    $result = null;
    foreach ($this as $index => $value) {
      $value = $iterator($value, $index);
      if ($result === null || $value >= $result)
        $result = $value;
    }
    return $result;
  }

  public function min($iterator = null) {
    $iterator = __def($iterator, '__lambda');
    $result = null;
    foreach ($this as $index => $value) {
      $value = $iterator($value, $index);
      if ($result === null || $value < $result)
        $result = $value;
    }
    return $result;
  }

  public function partition($iterator = null) {
    $iterator = __def($iterator, '__lambda');
    $trues = new RArray; $falses = new RArray;
    foreach ($this as $index => $value) {
      $iterator($value, $index) ? $trues->push($value) : $falses->push($value);
    }
    return _A($trues, $falses);
  }

  public function pluck($property) {
    $results = new RArray;
    foreach ($this as $index => $value) {
      $results->push($value[$property]);
    }
    return $results;
  }

  public function reject($iterator) {
    $results = new RArray;
    foreach ($this as $index => $value) {
      if (!$iterator($value, $index))
        $results->push($value);
    }
    return $results;
  }

  public function uniq($sorted = false) {
    return $this->inject(new RArray, function($array, $value, $index) use($sorted) {
      if (0 == $index || ($sorted ? $array->last() != $value : !$array->has($value)))
        $array->push($value);
      return $array;
    });
  }

  public function intersect(RArray $array) {
    return $this->uniq()->select(function($item) use($array) {
      return $array->detect(function($value) use($item) {
        return $item === $value;
      });
    });
  }

}

function _A() {
  $args = func_get_args();
  if (count($args) === 0) {
    return new RArray(array());
  }
  elseif (count($args) === 1 && is_array($args[0])) {
    return new RArray($args[0]);
  }
  return new RArray($args);
}

function _R($from, $to, $inclusive = true) {
  $array = new RArray;
  $to = $inclusive ? $to : $to - 1;
  for ($i = $from; $i <= $to; $i++)
    $array[] = $i;
  return $array;
}

function _W($wordlist) {
  return _S($wordlist)->trim()->split("/\s+/");
}

class RString extends RClass {
  public function __construct($string) {
    $this->__ = $string;
  }

  public function __toString() {
    return (string) $this->__;
  }

  public function native() {
    return (string) $this->__;
  }

  public function trim() {
    return new RString(trim($this->__));
  }

  public function split($regex) {
    $arr = new RArray(preg_split($regex, $this->__));
    return $arr->collect(function($value, $index) {
      return new RString($value);
    });
    return $arr;
  }

  public function length() {
    return strlen($this->__);
  }
  public function size() { return $this->length(); }

  public function times($count) {
    return new RString(str_repeat($this->__, $count));
  }
}

function _S($string) {
  return new RString($string);
}

/////// RInt

class RInt extends RClass {
  public function __construct($number) {
    $this->__ = (int)$number;
  }

  public function __toString() {
    return (int) $this->__;
  }

  public function native() {
    return (int) $this->__;
  }

  public function succ() {
    return new RInt($this->__ + 1);
  }

  public function times($iterator) {
    for ($i = 0; $i < $this->__; $i++) {
      $iterator($i, $this->__);
    }
  }

  public function isPrime() {
    if (2 > $this->__) return false;
    if (0 == $this->__ % 2) return (2 == $this->__);
    for ($index = 3; $this->__ / $index > $index; $index += 2)
      if (0 == $this->__ % $index) return false;
    return true;
  }
}

function _N($number) {
  return new RInt($number);
}

function _randArray($length, $min, $max) {
  $array = _A();
  for ($i = 0; $i < $length; $i++)
    $array->push(rand($min, $max));
  return $array;
}

?>