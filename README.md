# php-json2sml

Converts json into SML document.

## What is SML?

> [Video - Using SML in PHP](https://dev.stenway.com/SML/PHP.html)

> [Guide - SML Specification](https://dev.stenway.com/SML/Specification.html)

> [Wikipedia (DE)](https://de.wikipedia.org/wiki/Simple_Markup_Language)

> [Video - SML in 60sec](https://www.youtube.com/watch?v=qOooyygwX0w)

> [Video - SML Explained](https://www.youtube.com/watch?v=fBzMdzMtH-s&t=221s)

## Using

### Given JSON structure
```php
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

// Returns the first value of attribute "lastName"
echo $root
    ->attribute("lastName")
    ->getValues()[0];

// Returns the first value of attribute "city" in element "address" 
echo $root
    ->element("address")
    ->attribute("city")
    ->getValues()[0];

// Returns the first value of attribute "number" of second element "phoneNumbers" 
echo $root
    ->element("phoneNumbers")
    ->elements()[1]
    ->attribute("number")
    ->getValues()[0];

// Returns the first value of attribute "children"
echo $root
    ->attribute("children")
    ->getValues()[0];
```

Result:

```shell
Smith
New York
646 555-4567
Aaron
```

### Generated SML document structure as string output

```php
# PHP
$doc->toString();
```

```shell
sml
	firstName John
	lastName Smith
	isAlive 1
	age 27
	address
		streetAddress "21 2nd Street"
		city "New York"
		state NY
		postalCode 10021-3100
	end
	phoneNumbers
		phoneNumber
			type home
			number "212 555-1234"
		end
		phoneNumber
			type office
			number "646 555-4567"
		end
	end
	children Aaron
	spouse 1
end
```

## Documentation

### JsonToSmlConverter
Creates a new instance of the JSON to SML converter

```php
$converter = new JsonToSmlConverter();
```

### convert
Static method "convert" will convert your JSON in a SML document. 

> convert(object $jsonObject): SmlDocument

```php
$smlDocument = $converter::convert(json_decode($json));
```
