<?php
/**
 * PHP example for JSON-W.
 *
 * @author Ivan Daunis
 *
 * Copyright (c) 2015 Use Labs, LLC.
 * All rights reserved.
 */

require_once "jsonw.php";

$json_example = json_decode('
[
  {
    "ID": 17,
    "Name": "Acme Corporation",
    "Address": "Nobel House, Regent Centre",
    "Manager": { "FirstName": "John", "LastName": "Doe" },
    "Employees": [
      { "FirstName": "Brian", "LastName": "Hunt" },
      { "FirstName": "Mick", "LastName": "Henning" }
    ]
  },
  {
    "ID": 18,
    "Name": "The Empire",
    "Address": "Milton Keynes Leisure Plaza",
    "Manager": { "FirstName": "Ana", "LastName": "Johnsnon" },
    "Employees": [
      { "FirstName": "Erick", "LastName": "O\'Neil" },
      { "FirstName": "George", "LastName": "Halloway" }
    ]
  }
]
');

$jsonw_string = jsonw_encode( $json_example );
$decoded_json = jsonw_decode( $jsonw_string );

print ( "\n\n\x1b[31;1mOriginal JSON:\x1b[0m\n" );
print ( json_encode( $json_example ) );

print ( "\n\n\x1b[32;1mJSON-W Representation:\x1b[0m\n" );
print ( $jsonw_string );

print ( "\n\n\x1b[31;1mDecoded JSON:\x1b[0m\n" );
print ( json_encode( $decoded_json ) );

print ( "\n\n" );
