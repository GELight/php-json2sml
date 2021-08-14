<?php

include_once "vendor/autoload.php";

use GELight\conversion\{JsonToSmlConverter};

$json = <<<JSON
'{
  "firstName": "John",
  "lastName": "Smith",
  "isAlive": true,
  "age": 27,
  "address": {
    "streetAddress": "21 2nd Street",
    "city": "New York",
    "state": "NY",
    "postalCode": "10021-3100"
	},
  "phoneNumbers": [
    {
      "type": "home",
      "number": "212 555-1234"
    },
    {
      "type": "office",
      "number": "646 555-4567"
    }
  ],
  "children": [],
  "spouse": null
}'
JSON;

$converter = new JsonToSmlConverter();
echo "<pre>";
var_export($converter::convert(json_decode($json)));
echo "</pre>";
