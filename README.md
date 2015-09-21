JSON-W
======

Introduction
------------

JSON-W(ordbook) is a JSON variant where the repeated occurrences of the
objects names are replaced by their respective references in a dynamic
dictionary.


Algorithm
---------
The *Wordbook* algorithm is performed by traversing the JSON document in BFS
(Breadth-first search) looking for previous occurrences of the object names in
a implicit dictionary which is being built at runtime. If an object name is
found in the dictionary the `base93` encoded reference preceded by the hash
symbol `#` is replaced by the redundant object name and the name is moved at
the end of the dictionary pushing up the subsequent references. Otherwise if
the name is not found yet in the dictionary is added at the end of the
dictionary. Building this way a model where the names that are most likely to
appear will have the same reference. Yet if the replaced reference already
exists in the newly created object structure, a star symbol `*` is used
instead, to define a list multiple references.

For example the JSON array on page 7 of [RFC 4627]
 (https://www.ietf.org/rfc/rfc4627.txt) becomes:
```json
[
  {
    "precision": "zip",
    "Latitude": 37.7668,
    "Longitude": -122.3959,
    "Address": "",
    "City": "SAN FRANCISCO",
    "State": "CA",
    "Zip": "94107",
    "Country": "US"
  },
  {"*0": ["zip", 37.371991, -122.02602, "", "SUNNYVALE", "CA", "94085", "US"]}
]
```

The *Wordbook* algorithm can be generalized and applied to many structured data
representations such as databases, data streaming, APIs, etc.

### Examples

This is a JSON array containing two objects which contain objects and arrays:
```json
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
      { "FirstName": "Erick", "LastName": "O'Neil" },
      { "FirstName": "George", "LastName": "Halloway" }
    ]
  }
]
```

This is the JSON-W representation which saves 26% of the data:
```json
[
  {
    "ID": 17,
    "Name": "Acme Corporation",
    "Address": "Nobel House, Regent Centre",
    "Manager": { "FirstName": "John", "LastName": "Doe" },
    "Employees": [
      { "*5": [ "Brian", "Hunt" ] },
      { "*5": [ "Mick", "Henning" ] }
    ]
  },
  {
    "*0": [
      18,
      "The Empire",
      "Milton Keynes Leisure Plaza",
      { "*5": [ "Ana", "Johnsnon" ] },
      [
        { "*5": [ "Erick", "O'Neil" ] },
        { "*5": [ "George", "Halloway" ] }
      ]
    ]
  }
]

```

This is an example of a Flikr JSON API file:
```json
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
```

In this case there are only 3 redundant object names, and the order of
appearance is not completely correlative but yet in JSON-W we can group
them together providing a 3% improvement:
```json
{
  "title": "Talk On Travel Pool",
  "link": "http://www.flickr.com/groups/talkontravel/pool/",
  "description": "Travel and vacation photos from around the world.",
  "modified": "2009-02-02T11:10:27Z",
  "generator": "http://www.flickr.com/",
  "items": [
    {
      "*0": [
        "View from the hotel",
        "http://www.flickr.com/photos/33112458@N08",
        "Talk On Travel has added a photo to the pool"
      ],
      "media": {"m": "http://farm4.static.flickr.com/3037/3081564649"},
      "date_taken": "2008-12-04T04:43:03-08:00",
      "published": "2008-12-04T12:43:03Z",
      "author": "nobody@flickr.com (Talk On Travel)",
      "author_id": "33112458@N08",
      "tags": "spain dolphins tenerife canaries lagomera aqualand cristines"
    }
  ]
}
```

This is an example of a iPhone Menu JSON file configuration:
```json
{
  "menu": {
    "header": "xProgress SVG Viewer",
    "items": [
      { "id": "Open" },
      { "id": "OpenNew", "label": "Open New" },
      null,
      { "id": "ZoomIn", "label": "Zoom In" },
      { "id": "ZoomOut", "label": "Zoom Out" },
      { "id": "OriginalView", "label": "Original View" },
      null,
      { "id": "Quality" },
      { "id": "Pause" },
      { "id": "Mute" },
      null,
      { "id": "Find", "label": "Find..." },
      { "id": "FindAgain", "label": "Find Again" },
      { "id": "Copy" },
      { "id": "CopyAgain", "label": "Copy Again" },
      { "id": "CopySVG", "label": "Copy SVG" },
      { "id": "ViewSVG", "label": "View SVG" },
      { "id": "ViewSource", "label": "View Source" },
      { "id": "SaveAs", "label": "Save As" },
      null,
      { "id": "Help" },
      { "id": "About", "label": "About xProgress CVG Viewer..." }
    ]
  }
}
```

In this case of missing "label" elements constantly rotate the dictionary
although it provides a 9% improvement:
```json
{
  "menu": {
    "header": "xProgress SVG Viewer",
    "items": [
      { "id": "Open" },
      { "#3": "OpenNew", "label": "Open New" },
      null,
      { "*3": ["ZoomIn", "Zoom In"] },
      { "*3": ["ZoomOut", "Zoom Out"] },
      { "*3": ["OriginalView", "Original View"] },
      null,
      { "#3": "Quality" },
      { "#4": "Pause" },
      { "#4": "Mute" },
      null,
      { "#4": "Find", "#3": "Find..." },
      { "*3": ["FindAgain", "Find Again"] },
      { "#3": "Copy" },
      { "#4": "CopyAgain", "#3": "Copy Again" },
      { "*3": ["CopySVG", "Copy SVG"] },
      { "*3": ["ViewSVG", "View SVG"] },
      { "*3": ["ViewSource", "View Source"] },
      { "*3": ["SaveAs", "Save As"] },
      null,
      { "#3": "Help" },
      { "#4": "About", "#3": "About xProgress CVG Viewer..." }
    ]
  }
}
```

Source
---

The source code is organized in folders providing the JSON-W encoder and
decoder implementation in different programming languages.


Code contributions
---

Note: by contributing code to the JSON-W project in any form, including
sendinga pull request via Github, a code fragment or patch via private email
or public discussion groups, you agree to release your code under the terms of
the BSD 3-Clause License.

#### How to provide a patch for a new feature

If it is a major feature or a semantical change, use the following procedure
to submit a patch:

1. Fork JSON-W on github ( http://help.github.com/fork-a-repo/ )
2. Create a topic branch (git checkout -b my_branch)
3. Push to your branch (git push origin my_branch)
4. Initiate a pull request on github
( http://help.github.com/send-pull-requests/ )
5. Done :)

For minor fixes just open a pull request on Github.

Enjoy!