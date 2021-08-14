<?php

namespace GELight\conversion;

use JetBrains\PhpStorm\Pure;
use Stenway\Sml\SmlElement;
use Stenway\Sml\SmlDocument;

class JsonToSmlConverter {

    public function __construct() {}

    /**
     * @param $jsonObject
     * @return SmlDocument
     */
    public static function convert($jsonObject): SmlDocument {

        $root = new SmlElement("SML");
		$doc = new SmlDocument($root);
        $settings = new JsonToSmlSettings();

		JsonToSmlConverter::convertObj($jsonObject, $root, $settings);

		if ($settings->case === 1) {
            $doc->setEndKeyword("end");
            $root->setName("sml");
        } else if ($settings->case === 2) {
            $doc->setEndKeyword("END");
        }
		if (count($doc->getRoot()->nodes) == 1 && $doc->getRoot()->nodes[0] instanceof SmlElement) {
            $doc->setRoot($doc->getRoot()->nodes[0]);
        }
		return $doc;
	}

	public static function convertObj($jsonObject, SmlElement $smlElement, JsonToSmlSettings $settings) {
		if (JsonToSmlConverter::isValue($jsonObject)) {
            $smlElement.addString("Value", $jsonObject);
        } else if (JsonToSmlConverter::isSimpleArray($jsonObject)) {
            $smlElement.addAttribute("Value", JsonToSmlConverter::getSimpleArrayValues($jsonObject));
        } else if (JsonToSmlConverter::isSimpleMatrix($jsonObject)) {
            $smlElement.addAttribute("Value", JsonToSmlConverter::getSimpleMatrixValues($jsonObject));
        } else if (JsonToSmlConverter::isComplexArray($jsonObject)) {
            $itemName = JsonToSmlConverter::getItemName(null);
            JsonToSmlConverter::convertComplexArray($jsonObject, $smlElement, $itemName, $settings);
        } else if (JsonToSmlConverter::isObject($jsonObject)) {
            JsonToSmlConverter::convertObjProperties($jsonObject, $smlElement, $settings);
        } else {
            $smlElement.addString("...", "...");
        }
	}

	public static function convertObjProperties($jsonObject, SmlElement $smlElement, JsonToSmlSettings $settings) {
        foreach ($jsonObject as $key) {
            echo $key."<br/>";

            $settings->scan($key);

            $value = $jsonObject[$key];
            if (JsonToSmlConverter::isValue($value)) {
                $smlElement->addString($key, $value);
            } else if (JsonToSmlConverter::isSimpleArray($value)) {
                $smlElement->addAttribute($key, JsonToSmlConverter::getSimpleArrayValues($value));
            } else if (JsonToSmlConverter::isSimpleMatrix($value)) {
                $smlElement->addAttribute($key, JsonToSmlConverter::getSimpleMatrixValues($value));
            } else if (JsonToSmlConverter::isComplexArray($value)) {
                $childSmlElement = $smlElement->addElement($key);
                $itemName = JsonToSmlConverter::getItemName($key);
                JsonToSmlConverter::convertComplexArray($value, $childSmlElement, $itemName, $settings);
            } else if (JsonToSmlConverter::isObject($value)) {
                $childSmlElement = $smlElement->addElement($key);
                JsonToSmlConverter::convertObjProperties($value, $childSmlElement, $settings);
            } else {
                $smlElement->addString("...", "...");
            }
        }
	}

	public static function convertComplexArray(array $array, SmlElement $smlElement, string $itemName, JsonToSmlSettings $settings) {
		foreach ($array as $item) {
            if (JsonToSmlConverter::isValue($item)) {
                $smlElement->addString($itemName, $item);
            } else if (JsonToSmlConverter::isSimpleArray($item)) {
                $smlElement->addAttribute($itemName, JsonToSmlConverter::getSimpleArrayValues($item));
            } else if (JsonToSmlConverter::isSimpleMatrix($item)) {
                $smlElement->addAttribute($itemName, JsonToSmlConverter::getSimpleMatrixValues($item));
            } else {
                $childSmlElement = $smlElement->addElement($itemName);
                JsonToSmlConverter::convertObj($item, $childSmlElement, $settings);
            }
        }
	}

	public static function replaceLast(string $name, int $length, $pattern): string {
        return substr($name, 0, -1) . $pattern;
	}

	public static function getItemName(string|null $name): string {
		if (!is_null($name) && count($name) > 0) {
            if ($name.endsWith("IES")) return JsonToSmlConverter::replaceLast($name,3,"Y");
            if ($name.endsWith("ies")) return JsonToSmlConverter::replaceLast($name,3,"y");
            if ($name.endsWith("S")) return JsonToSmlConverter::replaceLast($name,1,"");
            if ($name.endsWith("s")) return JsonToSmlConverter::replaceLast($name,1,"");
        }
		return "item";
	}

	public static function getSimpleArrayValues(array $simpleArray): array {
		if (count($simpleArray) === 0) {
            return [null];
        }
		return array_map(function ($x) {
		    return JsonToSmlConverter::valueToString($x);
        }, $simpleArray);
	}

	public static function getSimpleMatrixValues($simpleArray): array {
		$result = [];
		foreach ($simpleArray as $item) {
            if (JsonToSmlConverter::isValue($item)) {
                $result[] = JsonToSmlConverter::valueToString($item);
            } else {
                $subValues = JsonToSmlConverter::getSimpleArrayValues($item);
                $result = array_merge($result, $subValues);
            }
        }
		return $result;
	}

	public static function valueToString($value): string|null {
        return is_null($value) ? null : "" . $value;
	}

	public static function isValue($jsonObject): bool {
		return $jsonObject == null ||
            is_numeric($jsonObject) ||
            is_string($jsonObject) ||
            is_bool($jsonObject);
	}

	#[Pure]
    public static function isSimpleArray($jsonObject): bool {
		if (!is_array($jsonObject)) {
            return false;
        }
		foreach ($jsonObject as $item) {
            if (!JsonToSmlConverter::isValue($item)) {
                return false;
            }
        }
		return true;
	}

	#[Pure]
    public static function isSimpleMatrix($jsonObject): bool {
		if (!is_array($jsonObject)) {
            return false;
        }
		foreach ($jsonObject as $item) {
            if (!JsonToSmlConverter::isSimpleArray($item)) {
                return false;
            }
        }
		$firstLength = count($jsonObject[0]);
        foreach ($jsonObject as $item) {
            if (count($item) != $firstLength) {
                return false;
            }
        }
		return true;
	}

	#[Pure]
    public static function isComplexArray($jsonObject): bool {
		return is_array($jsonObject) && !JsonToSmlConverter::isSimpleArray($jsonObject);
	}

	public static function isObject($jsonObject):bool {
		return !is_null($jsonObject) && !is_array($jsonObject) && gettype($jsonObject) === "object";
	}

}
