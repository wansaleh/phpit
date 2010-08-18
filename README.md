# PHPit!

This library is a simple wrapper to the beautiful syntax of Ruby.
PHPit! drew inspiration from [Prototype Javascript Library](www.prototypejs.com) which ported Ruby's Enumerable functions to Javascript.

# `RArray`

## Methods
### `visualize()`
Returns a string representation of the `RArray` object. It is a recursive function.

### `inspect()`
Alias for `visualize()`



# Requirements

* PHP > 5.3.0

PHP 5.3.0 introduces the lambda style (anonymous) function to be used as a parameter of another function. PHPit! exploits this new feature to port the Enumerable methods in Ruby to PHP.

# Discaimer

The creation of this library doesn't mean that PHP by itself can beat Ruby's simplicity.