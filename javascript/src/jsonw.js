/**
 * JavaScript implementation of JSON-W.
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

var JSONW = {

  is_object: function (obj) {
    if( (typeof obj === "object") && (obj !== null) ) {
      return true;
    }
    return false;
  },

  is_array: function (obj) {
    if( Object.prototype.toString.call( obj ) === '[object Array]' ) {
      return true;
    }
    return false;
  },

  clone: function (obj) {
    var copy;

    // Handle the 3 simple types, and null or undefined
    if (null === obj || "object" != typeof obj) return obj;

    // Handle Array
    if (obj instanceof Array) {
      copy = [];
      for (var i = 0, len = obj.length; i < len; i++) {
        copy[i] = this.clone(obj[i]);
      }
      return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
      copy = {};
      for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = this.clone(obj[attr]);
      }
      return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
  },

  array_flip: function( arr ) {
    var result = [];
    for ( var key in arr ) {
      if ( arr.hasOwnProperty( key ) ) {
        result[arr[key]] = key;
      }
    }
    return result;
  },

  /**
   * Converts an integer into a base93 string.
   *
   * @param num the number to convert.
   *
   * @return the base93 encoded string.
   */
  base93_encode: function(num) {
    var codes = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz "+
      "^'`!|~<>()[]{}@_$%&,./:;+-=?#*";
    if ( num === 0 ) {
      return codes[0];
    }
    var arr = [];
    while ( num ) {
      var rem = num % 93;
      num =  Math.floor( num / 93 );
      arr.push( codes[rem] );
    }
    return arr.reverse().join('');
  },

  /**
   * Converts a base93 string into an integer.
   *
   * @param string the base93 encoded string.
   *
   * @return the decoded integer.
   */
  base93_decode: function(string) {
    var codes = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz "+
      "^'`!|~<>()[]{}@_$%&,./:;+-=?#*";
    var len = string.length;
    var num = 0;
    var idx = 0;
    var tebahpla = this.array_flip(codes.split(''));
    for( var i=0; i<len; i++ ) {
      var c = string[i];
      var power = (len - (idx + 1));
      num += tebahpla[c] * (Math.pow(93,power));
      idx += 1;
    }
    return num;
  },

  /**
   * Encodes a JavaScript object into a JSON-W object.
   *
   * @param object the JavaScript object to encode.
   *
   * @return the JSON-W encoded object.
   */
  encode: function(object) {
    var count = 0;
    var dict = [];
    var queue = [];

    var object_copy = this.clone(object);

    queue.push( object_copy );

    while (queue.length > 0) {
      var obj = queue.shift();

      var keys = [];
      var values = [];
      for (var k in obj) {
        keys.push( k );
        values.push( obj[k] );
      }

      for (var n=0; n<keys.length; n++) {
        var key = keys[n];
        var value = values[n];

        if ( this.is_object(value) || this.is_array(value) ) {
          queue.push( value );
        }

        if ( this.is_object(obj) && !this.is_array(obj) ) {

          if ( key in dict ) {

            delete obj[key];
            var ref = this.base93_encode(dict[key]);
            var single = "#" + ref;
            var multiple = "*" + ref;

            if ( single in obj || multiple in obj ) {
              if ( multiple in obj ) {
                obj[multiple].push(value);
              } else {
                obj[multiple] = [obj[single], value];
                delete obj[single];
              }
            } else {
              obj[single] = value;
            }

            // Move back
            for (var j in dict) {
              if( dict[j] > dict[key] ) {
                dict[j]--;
              }
            }
            dict[key] = count-1;

          } else {

            // Need to keep the same order when encoding
            delete obj[key];
            obj[key] = value;

            // Add key to the end of the dictionary
            dict[key] = count;
            count ++;
          }
        }
      }
    }

    return object_copy;
  },

  /**
   * Decodes a JSON-W object into a JavaScript object.
   *
   * @param object the JSON-W object to decode.
   *
   * @return the decoded JavaScript object.
   */
  decode: function(object) {

    var object_copy = this.clone(object);

    var dict = [];
    var queue = [];
    queue.push( object_copy );

    while (queue.length > 0) {
      var obj = queue.shift();

      var keys = [];
      var values = [];
      for (var k in obj) {
        keys.push( k );
        values.push( obj[k] );
      }

      for (var n=0; n<keys.length; n++) {
        var key = keys[n];
        var value = values[n];

        if ( this.is_object(value) || this.is_array(value) ) {
          queue.push( value );
        }

        if ( this.is_object(obj) && !this.is_array(obj) ) {
          if ( key[0] != "*" && key[0] != "#" ) {
            // Try keeping the same order when decoding
            delete obj[key];
            obj[key] = value;
            // Add key to the end of the dictionary
            dict.push(key);
          } else {
            delete obj[key];

            var idx = this.base93_decode( key.substring(1) );
            if ( idx >= dict.length ) {
              return NULL;
            }

            if ( key[0] == "#" ) {
              value = [value];
            }

            for ( var v in value ) {
              var element = dict[idx];
              obj[element] = value[v];

              // Move back
              var last = dict.length-1;
              var copy = dict[idx];
              for ( var i=idx; i<last; i++) {
                dict[i] = dict[i+1];
              }
              dict[last] = copy;
            }

          }
        }
      }
    }

    return object_copy;
  }

};