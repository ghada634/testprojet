<?php

$var = [1,2,3];
$var = [
    1,
    2,
    3
];
$var = [
    1,
    2,
    /* three */ 3,
];
$var = [
1,
        2,
    /* three */ 3,
 ];

$var = [
    1 => 'one',
    2 => 'two',
    3 => 'three'
];
$var = [
    1 => 'one',
    2 => 'two',
    /* three */ 3 => 'three',
];
$var = [
1 => 'one',
        2 => 'two',
    /* three */ 3 => 'three',
    ];

$var = array(
    'one' => function () {
        $foo = [1,2,3];
        $bar = [
            1,
            2,
            3];
    },
    'two' => 2,
);

return [
    [
        'foo' => true,
    ]
];

$array = [
    'foo' => 'foo',
    'bar' => $baz ?
        ['abc'] :
        ['def'],
    'hey' => $baz ??
        ['one'] ??
        ['two'],
    'fn' =>
        fn ($x) => yield 'k' => $x,
  $a ?? $b,
  $c ? $d : $e,
];

$foo =
[
    'bar' =>
   [
    ],
];

$foo = [
    'foo'
        . 'bar',
    [
            'baz',
            'qux',
        ],
];

// phpcs:set Generic.Arrays.ArrayIndent indent 2

$var = [
1 => 'one',
        2 => 'two',
    /* three */ 3 => 'three',
    ];

// phpcs:set Generic.Arrays.ArrayIndent indent 4

$array = array(
    match ($test) {
    1 => 'a', 2 => 'b'
    }
          => 'dynamic keys, woho!',
);

$array = [
  match ($test) {
    1 => 'a', 2 => 'b'
  }
       => 'dynamic keys, woho!',
];

// Ensure that PHP 8.0 named parameters don't affect the sniff.
$array = [
    functionCall(
        name: $value
    ),
];

$array = [
  functionCall(
      name: $value
  ),
];

// phpcs:set Generic.Arrays.ArrayIndent indent 1

// Testing pluralization of indent text - open brace indent.
 $var =
[
 1 => 'one',
 ];

// Testing pluralization of indent text - array item indent.
 $var = [
 1 => 'one',
      2 => 'two',
    /* three */ 3 => 'three',
 ];

// Testing pluralization of indent text - close brace indent.
 $var = [
  1 => 'one',
  ];

// phpcs:set Generic.Arrays.ArrayIndent indent 0

// No test for open brace indent as that is _minimum_ and any actual value will be 0 or more, so with indent 0, this will never yield an error.

// Testing pluralization of indent text - array item indent.
 $var = [
 1 => 'one',
      2 => 'two',
    /* three */ 3 => 'three',
 ];

// Testing pluralization of indent text - close brace indent.
 $var = [
 1 => 'one',
  ];

// phpcs:set Generic.Arrays.ArrayIndent indent 4

 $array = [1,
 ];
