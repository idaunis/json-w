# JSON-W JavaScript Implementation

## Usage
Simply include `jsonw.min.js` in your project and invoke `JSONW.encode(...)` to encode and `JSONW.decode(...)` to decode.
For example:

```js
<script src="dist/jsonw.min.js"></script>
<script type="text/javascript">
  // JSON-W encoded JavaScript object
  var data = '[{"ID":17,"Name":"Acme Corporation","Address":"Nobel House, Regent Centre","Manager":{"FirstName":"John","LastName":"Doe"},"Employees":[{"*5":["Brian","Hunt"]},{"*5":["Mick","Henning"]}]},{"*0":[18,"The Empire","Milton Keynes Leisure Plaza",{"*5":["Ana","Johnsnon"]},[{"*5":["Erick","O\'Neil"]},{"*5":["George","Halloway"]}]]}]';

  // Decoded JavaScript object
  data = JSONW.decode(data);
</script>
```

Code contributions
---

Note: by contributing code to the JSON-W project in any form, including
sending a pull request via Github, a code fragment or patch via private email
or public discussion groups, you agree to release your code under the terms of
the BSD 3-Clause License.

### Build Instructions

Install [Node.js](https://nodejs.org/download) and run the following commands:

```sh
# First time set up
npm install

# Build using
grunt
```

After build, the library will be in the `dist` folder.

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