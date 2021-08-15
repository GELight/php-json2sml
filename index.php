<?php

include_once "vendor/autoload.php";

use GELight\conversion\{JsonToSmlConverter};

$json = <<<JSON
{
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
  "children": [
    "Aaron"
  ],
  "spouse": true
}
JSON;

$converter = new JsonToSmlConverter();
$doc = $converter::convert(json_decode($json));
$root = $doc->getRoot();

echo $root
    ->attribute("lastName")
    ->getValues()[0];

echo $root
    ->element("address")
    ->attribute("city")
    ->getValues()[0];

echo $root
    ->element("phoneNumbers")
    ->elements()[1]
    ->attribute("number")
    ->getValues()[0];

echo $root
    ->attribute("children")
    ->getValues()[0];

//echo "<pre>";
//var_export($doc->toString());
//echo "</pre>";