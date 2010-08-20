# PHPit!

This library is a simple port of the beautiful syntax of Ruby.
PHPit! drew inspiration from [Prototype Javascript Library](www.prototypejs.com) which ported [Ruby's](www.ruby-lang.org) [Enumerable](http://ruby-doc.org/core/classes/Enumerable.html) functions to Javascript.

# Usage

## Array creation

In Ruby, creating arrays are as simple as:

    array = [1, 2, 3, 4, 5];

PHPit! enables easier syntax for array creation:

    // PHPit!
    $array = _A(1, 2, 3, 4, 5);
    // or in native PHP code:
    $array = array(1, 2, 3, 4, 5);

Some would say: "It's just `array()` to `_A()` shorthand, eh?". Yes, it's just a shorthand, but with steroids.

The returned array from `_A()` shorthand function is not actually an array. It's an instance to an `RArray` object which contains many useful methods, borrowed from Ruby's Enumerable class.

## Range... in PHP!

Ruby users are happy with this syntax:

    range = (1..100);

PHP developers now can smile with this syntax:

    // PHPit!
    $range = _R(1, 100);
    // which is the same as:
    $range = array(1, 2, 3, ..., 99, 100);

## Space delimited strings

Ruby programmers does this (might be familiar to Perl programmers):

    words = %w{hello world this is nice};

Now PHP users can do this:

    // PHPit!
    $words = _W("hello world this is nice");
    // the same as this native PHP code:
    $words = array("hello", "world", "this", "is", "nice");


## Here comes the Ruby steroid to PHP

Ruby iteration is damn easy:

    words = %w{hello world this is nice};
    words.each do |word|
      puts word + " -> "
    end
    # hello -> world -> this -> is -> nice ->

You know what? PHP can do that also!

    // PHPit!
    $words = _W("hello world this is nice");
    $words->each(function($word) {
      print $word . " -> ";
    });
    // hello -> world -> this -> is -> nice ->

Almost similar, right? This type of syntax is made possible because of the recent changes in PHP 5.3.0. This code does not work in PHP &lt; 5.3.0.

## Ruby enumerable in PHP

### Ruby inject

    result = [1, 2, 3, 4].inject(0) do |total, value|
      total + value
    end
    # result = 10

### PHP inject

    // PHPit!
    $result = _A(1, 2, 3, 4)->inject(0, function($total, $value) {
      return $total + $value;
    });
    // $result = 10

### Ruby select

    result = (1..10).select do |value|
      value % 3 == 0
    end
    # result = [3, 6, 9]

### PHP select

    // PHPit!
    $result = _R(1, 10)->select(function($value) {
      return $value % 3 == 0;
    });
    // $result = array(3, 6, 9)

No matter what we do, it's impossible to beat Ruby's simplicity.

## Some caveat:

PHP's nature not to have global variables inside any function. If we run this:

    $connector = " ---- ";
    _W("hello world this is nice")->each(function($word) {
      print $word . $connector;
    });

PHP will produce notices because variable $connector is not recognized by the iterator function. To overcome this, PHP introduces the `use` keyword. "Use" it like so:

    _W("hello world this is nice")->each(function($word) use($connector) {
      print $word . $connector;
    });

The use keyword introduces `$connector` to the function. Just like PHP's `global` keyword.

# RArray

## Methods

### visualize()

Returns a string representation of the `RArray` object. It is a recursive function.

### inspect()

Alias for `visualize()`

### get($index)

Returns the element at the specified `$index`.

### native()

Returns PHP's native array from the `RArray` object.

# Requirements

* PHP > 5.3.0

PHP 5.3.0 introduces the lambda style (anonymous) function to be used as a parameter of another function. PHPit! exploits this new feature to port the Enumerable methods in Ruby to PHP.

# Discaimer

The creation of this library doesn't mean that PHP by itself can beat Ruby's simplicity.
Copyright is for losers. Brainchild of Prototype JS Framework. Goodness of Ruby. I implemented them in PHP.