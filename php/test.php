<?php
/**
 * PHP test unit for JSON-W.
 *
 * @author Ivan Daunis
 *
 * Copyright (c) 2015 Use Labs, LLC.
 * All rights reserved.
 */

require_once "jsonw.php";

$test1 = json_decode('
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

$test2 = json_decode('
[
  {
    "precision": "zip",
    "Latitude":  37.7668,
    "Longitude": -122.3959,
    "Address":   "",
    "City":      "SAN FRANCISCO",
    "State":     "CA",
    "Zip":       "94107",
    "Country":   "US"
  },
  {
    "precision": "zip",
    "Latitude":  37.371991,
    "Longitude": -122.026020,
    "Address":   "",
    "City":      "SUNNYVALE",
    "State":     "CA",
    "Zip":       "94085",
    "Country":   "US"
  }
]
');

$test3 = json_decode('
{
  "menu": {
    "header": "xProgress SVG Viewer",
    "items": [
      {
        "id": "Open"
      },
      {
        "id": "OpenNew",
        "label": "Open New"
      },
      null,
      {
        "id": "ZoomIn",
        "label": "Zoom In"
      },
      {
        "id": "ZoomOut",
        "label": "Zoom Out"
      },
      {
        "id": "OriginalView",
        "label": "Original View"
      },
      null,
      {
        "id": "Quality"
      },
      {
        "id": "Pause"
      },
      {
        "id": "Mute"
      },
      null,
      {
        "id": "Find",
        "label": "Find..."
      },
      {
        "id": "FindAgain",
        "label": "Find Again"
      },
      {
        "id": "Copy"
      },
      {
        "id": "CopyAgain",
        "label": "Copy Again"
      },
      {
        "id": "CopySVG",
        "label": "Copy SVG"
      },
      {
        "id": "ViewSVG",
        "label": "View SVG"
      },
      {
        "id": "ViewSource",
        "label": "View Source"
      },
      {
        "id": "SaveAs",
        "label": "Save As"
      },
      null,
      {
        "id": "Help"
      },
      {
        "id": "About",
        "label": "About xProgress CVG Viewer..."
      }
    ]
  }
}
');

$test4 = json_decode('
{
  "title": "Talk On Travel Pool",
  "link": "http://www.flickr.com/groups/talkontravel/pool/",
  "description": "Travel and vacation photos from around the world.",
  "modified": "2009-02-02T11:10:27Z",
  "generator": "http://www.flickr.com/",
  "items": [
    {
    "title": "View from the hotel",
    "link": "http://www.flickr.com/photos/33112458@N08",
    "media": {"m":"http://farm4.static.flickr.com/3037/3081564649"},
    "date_taken": "2008-12-04T04:43:03-08:00",
    "description": "Talk On Travel has added a photo to the pool",
    "published": "2008-12-04T12:43:03Z",
    "author": "nobody@flickr.com (Talk On Travel)",
    "author_id": "33112458@N08",
    "tags": "spain dolphins tenerife canaries lagomera aqualand cristines"
    }
  ]
}
');

function run_test($json_object) {

  $jsonw_string = jsonw_encode( $json_object );
  $decoded_object = jsonw_decode( $jsonw_string );

  $expected = json_encode( $json_object );
  $got = json_encode( $decoded_object );

  print ( "Test: ".substr($got, 0, 60)."... " );
  print ( strlen($got)." Bytes " );
  printf( "(%d%% Less) ", ((strlen($got)-strlen($jsonw_string))/strlen($got))*100 );

  if ( $expected == $got ) {
    print ( "Same Order. " );
  } else {
    print ( "Order Changed. " );
  }

  if ( strlen($expected) == strlen($got) ) {
    print ( "\x1b[32;1mPassed!\x1b[0m\n" );
  } else {
    print ( "\x1b[31;1mFail!\x1b[0m\n" );
  }
}

run_test( $test1 );
run_test( $test2 );
run_test( $test3 );
run_test( $test4 );
