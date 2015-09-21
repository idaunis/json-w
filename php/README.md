# JSON-W PHP Implementation

## Documentation

### `function base93_encode($num)`

Converts an integer into a base93 string.

 * **Parameters:** `$num` — `int` — the number to convert.
 * **Returns:** the base93 encoded string.

### `function base93_decode($string)`

Converts a base93 string into an integer.

 * **Parameters:** `$string` — `string` — the base93 encoded string.
 * **Returns:** the decoded integer.

### `function jsonw_last_error()`

Returns the last error occured during the last JSON-W encoding/decoding.

 * **Returns:** an integer, the value can any of the json_last_error() values
 plus:

| Constant          | Meaning                                 |
| ----------------- | ----------------------------------------|
| JSONW_ERROR_INDEX | Index not found in the current wordbook |

### `function jsonw_encode($object)`

Encodes a PHP object into a JSON-W string.

 * **Parameters:** `$object` — `object` — the PHP object to encode.
 * **Returns:** the JSON-W encoded string.

### `function jsonw_decode($string)`

Decodes a JSON-W string into a PHP object.

 * **Parameters:** `$string` — `string` — the JSON-W string to decode.
 * **Returns:** the decoded PHP Object.
