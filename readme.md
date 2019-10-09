PHP Standard Library
====================

Playing around with some new basic classes for FP development in PHP.

### Features

Functional programming with typical methods via `Stream` class and type-safe objects with `Struct` class.
Various small classes and helpers will be developed over time.

### Pair Class

Very simple way of storing two values.

```
// Create Pair
$font = Pair("Inconsolata", "monospace");
print($font -> first . " is type " . $font -> second);
```

### Stream Class

Streams contain a sequence of data and provides access to chainable, functional methods.

```
// Create Stream
Stream([
    'apple' => ['colour' => ['green', 'red'], 'isFruit' => true],
    'cabbage' => ['colour' => ['green'], 'isFruit' => false],
    'carrot' => ['colour' => ['orange'], 'isFruit' => true],
    'cucumber' => ['colour' => ['green'], 'isFruit' => false],
    'orange' => ['colour' => ['orange'], 'isFruit' => true],
    'pineapple' => ['colour' => ['yellow', 'brown'], 'isFruit' => true]
])

// Add Tomato
-> add('tomato', [
    'colour' => ['red', 'yellow', 'green'],
    'isFruit' => true
])

// Reject Orange
-> reject(function($_, $v) {
    return in_array('orange', $v['colour']);
})

// Filter Fruit
-> filter(function($_, $v) {
    return $v['isFruit'];
})

// Map Colours
-> map(function($_, $v) {
    return implode(", ", $v['colour']);
})

// Print Each
-> forEach(function($k, $v) {
    print("$k = $v \n");
});

/* prints the following;
   apple = green, red 
   pineapple = yellow, brown 
   tomato = red, yellow, green 
*/
```