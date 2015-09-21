<?php
/**
 * PHP implementation of JSON-W.
 *
 * @author Ivan Daunis
 *
 * BSD 3-Clause License
 * Copyright (c) 2015 Use Labs, LLC.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in the
 * documentation and/or other materials provided with the distribution.
 *
 * Neither the name of Use Labs, LLC. nor the names of its contributors
 * may be used to endorse or promote products derived from this software
 * without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
 * PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Converts an integer into a base93 string.
 *
 * @param int $num the number to convert.
 *
 * @return the base93 encoded string.
 */
function base93_encode($num)
{
  $codes = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz ".
    "^'`!|~<>()[]{}@_$%&,./:;+-=?#*";
  if ( $num == 0 ) {
    return $codes[0];
  }
  $arr = array();
  while ( $num ) {
    $rem = $num % 93;
    $num = (int) ($num / 93);
    $arr[] = $codes[$rem];
  }
  return implode(array_reverse($arr));
}

/**
 * Converts a base93 string into an integer.
 *
 * @param string $string the base93 encoded string.
 *
 * @return the decoded integer.
 */
function base93_decode($string)
{
  $codes = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz ".
    "^'`!|~<>()[]{}@_$%&,./:;+-=?#*";
  $len = strlen($string);
  $num = 0;
  $idx = 0;
  $tebahpla = array_flip(str_split($codes));
  for( $i=0; $i<$len; $i++ ) {
    $char = $string[$i];
    $power = ($len - ($idx + 1));
    $num += $tebahpla[$char] * (pow(93,$power));
    $idx += 1;
  }
  return $num;
}

/**
 * Returns the last error occured during the last JSON-W encoding/decoding.
 *
 * @return an integer, the value can any of the json_last_error() values plus:
 *          JSONW_ERROR_INDEX   Index not found in the current wordbook
 */
function jsonw_last_error()
{
  if ( isset($GLOBALS["JSONW_ERROR"]) ) {
    return $GLOBALS["JSONW_ERROR"];
  }
  return json_last_error();
}

/**
 * Encodes a PHP object into a JSON-W string.
 *
 * @param object $object the PHP object to encode.
 *
 * @return the JSON-W encoded string.
 */
function jsonw_encode($object)
{
  unset( $GLOBALS["JSONW_ERROR"] );
  $object_copy = unserialize( serialize( $object ) );

  $count = 0;
  $dict = array();
  $queue = new SplQueue();
  $queue->enqueue( $object_copy );

  while( !$queue->isEmpty() ) {
    $obj = $queue->dequeue();

    $keys = array();
    $values = array();
    foreach ($obj as $key => $value) {
      $keys[] = $key;
      $values[] = $value;
    }

    for ($n=0; $n<count($keys); $n++) {
      $key = $keys[$n];
      $value = $values[$n];
      if ( is_object($value) || is_array($value) ) {
        $queue->enqueue( $value );
      }
      if ( is_object($obj) ) {
        if ( isset($dict[$key]) ) {
          unset($obj->$key);
          $ref = base93_encode($dict[$key]);
          $single = "#".$ref;
          $multiple = "*".$ref;

          if (isset($obj->$single) || isset($obj->$multiple)) {
            if (isset($obj->$multiple)) {
              array_push($obj->$multiple, $value);
            } else {
              $obj->$multiple = array($obj->$single, $value);
              unset($obj->$single);
            }
          } else {
            $obj->$single = $value;
          }

          // Move back
          foreach ($dict as $k => $v) {
            if( $v > $dict[$key] ) {
              $dict[$k]--;
            }
          }
          $dict[$key] = $count-1;

        } else {
          // Need to keep the same order when encoding
          unset($obj->$key);
          $obj->$key = $value;
          // Add key to the end of the dictionary
          $dict[$key] = $count;
          $count ++;
        }
      }
    }
  }

  return json_encode( $object_copy );
}

/**
 * Decodes a JSON-W string into a PHP object.
 *
 * @param string $string the JSON-W string to decode.
 *
 * @return the decoded PHP Object.
 */
function jsonw_decode($string)
{
  unset( $GLOBALS["JSONW_ERROR"] );

  $object = json_decode($string);

  if ($object == NULL) {
    return NULL;
  }

  $dict = array();
  $queue = new SplQueue();
  $queue->enqueue( $object );

  while( !$queue->isEmpty() ) {
    $obj = $queue->dequeue();

    $keys = array();
    $values = array();
    foreach ($obj as $key => $value) {
      $keys[] = $key;
      $values[] = $value;
    }

    for ($n=0; $n<count($keys); $n++) {
      $key = $keys[$n];
      $value = $values[$n];
      if ( is_object($value) || is_array($value) ) {
        $queue->enqueue( $value );
      }
      if ( is_object($obj) ) {
        if ($key[0] != "*" && $key[0] != "#") {
          // Try keeping the same order when decoding
          unset($obj->$key);
          $obj->$key = $value;
          // Add key to the end of the dictionary
          $dict[] = $key;
        } else {
          unset($obj->$key);

          $idx = base93_decode(substr($key, 1));
          if ( $idx >= count($dict) ) {
            define("JSONW_ERROR_INDEX", 100);
            $GLOBALS["JSONW_ERROR"] = JSONW_ERROR_INDEX;
            return NULL;
          }

          if ($key[0] == "#") {
            $value = array($value);
          }

          foreach ($value as $v) {
            $element = $dict[$idx];
            $obj->$element = $v;

            // Move back
            $last = count($dict)-1;
            $copy = $dict[$idx];
            for ($i=$idx; $i<$last; $i++) {
              $dict[$i] = $dict[$i+1];
            }
            $dict[$last] = $copy;
          }


        }
      }
    }
  }
  return $object;
}

?>