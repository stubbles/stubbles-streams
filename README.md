stubbles/streams
=================

Input- and OutputStreams.


Build status
------------

[![Build Status](https://secure.travis-ci.org/stubbles/stubbles-streams.png)](http://travis-ci.org/stubbles/stubbles-streams) [![Coverage Status](https://coveralls.io/repos/github/stubbles/stubbles-streams/badge.svg?branch=master)](https://coveralls.io/github/stubbles/stubbles-streams?branch=master)

[![Latest Stable Version](https://poser.pugx.org/stubbles/streams/version.png)](https://packagist.org/packages/stubbles/streams) [![Latest Unstable Version](https://poser.pugx.org/stubbles/streams/v/unstable.png)](//packagist.org/packages/stubbles/streams)


Installation
------------

_stubbles/streams_ is distributed as [Composer](https://getcomposer.org/)
package. To install it as a dependency of your package use the following
command:

    composer require "stubbles/streams": "^9.0"


Requirements
------------

_stubbles/streams_ requires at least PHP 7.3.

For using encoding and decoding decorated streams the PHP extension _iconv_ is
required.

For the `stubbles\streams\linesOf()` and `stubbles\streams\nonEmptyLinesOf()`
functions the package _[stubbles/sequence](https://github.com/stubbles/stubbles-sequence)_
is required.


Interfaces and methods
----------------------

_stubbles/streams_ provides input and output streams for different kind of
sources. All input streams implement the `stubbles\streams\InputStream`
interface, all output streams the `stubbles\streams\OutputStream` interface.
While input streams can be used to read data from a source, output streams can
be used to write data to a certain source.

### Interfaces and their methods

#### Input stream methods

The input stream interface provides the following methods:

 * `read($length = 8192)` - returns given amount of bytes
 * `readLine($length = 8192)` - returns all characters up to given length until
   next line break
 * `bytesLeft()` - returns the amount of bytes left to be read
 * `eof()` - checks whether end of input was reached
 * `close()` - closes the stream

#### Output stream methods

The output stream interface provides the following methods:

 * `write($bytes)` - writes given bytes and returns amount of written bytes
 * `writeLine($bytes)` - same as `write()`, but adds a line break at end of bytes
 * `close()` - closes the stream

#### Seekable streams

Some streams are seekable which means you can go from one position in the stream
to another. Input streams which are seekable implement the
`stubbles\streams\Seekable` interface. It provides the following methods:

 * `seek($offset, $whence = Seekable::SET)` - sets internal stream pointer to
   given position
 * `tell()` - returns the current position of the internal stream pointer


#### Copy from input to output stream
_Available since release 8.1.0_

The shortcut function `stubbles\streams\copy()` provides a simple way to copy everything what's
in an input stream to an output stream:

```php
copy($in)->to($out);
```

Please note that copying starts at the offset where the input stream currently
is located, and changes the offset of the input stream to the end of the stream.
In case a non-seekable input stream is copied it can not return to its initial
offset.


### Decorating streams

#### Encoding-related streams

Sometimes it is necessary to decode input stream data into the internal encoding
of the application. While Stubbles' internal encoding is UTF-8, all data read
from input streams should be UTF-8 itself or at least converted to UTF-8 before
returned from the input stream. To ease this, the
`stubbles\streams\DecodingInputStream` class decorates another input stream
given as first parameter on construction, and tries to decode the data read
from the decorated input stream from the stream encoding to UTF-8 using `iconv()`.
However you need to specify the charset of the decorated input stream as second
parameter to the constructor:

```php
$decodingInputStream = new DecodingInputStream($encodedInputStream, 'iso-8859-1');
```

*Note: since release 9.0.0 a third parameter can be used to influence what the data is being encoded to. The default is still UTF-8.*

Of course there must be a possibility to write back into the correct encoding.
For this, the `stubbles\streams\EncodingOutputStream` class can be used. It
tries to convert from internal UTF-8 into the encoding of the decorated output
stream using `iconv()`.

```php
$encodingOutputStream = new EncodingOutputStream($encodedOutputStream, 'iso-8859-1');
```

*Note: since release 9.0.0 a third parameter can be used to influence what the data is being encoded from. The default is still UTF-8.*


File related streams
--------------------

To read data from a file one may use the `stubbles\streams\file\FileInputStream`
class. Its constructor expects either a name of a file, or an already opened
file pointer resource. If it is the name of the file the second parameter of the
constructor sets the mode in which the file is opened, it defaults to _rb_
(binary reading). The file input stream is a seekable stream.

To write data to a file one may use the `stubbles\streams\file\FileOutputStream`
class. Similarly to the file input stream class its constructor expects either a
name of a file, or an already opened file pointer resource. If it is the name of
the file the second parameter of the constructor sets the mode in which the file
is opened, it defaults to _wb_ (binary writing).

Warning: the mode parameter accepts modes which might not make any sense with
the stream class - e.g. the input stream allows _wb_ as value for the mode
parameter, but then you can not read from the given file, and vice versa for the
output stream.


Memory streams
--------------

Sometimes it is helpful if one can read or write data into memory. For such
purposes the `stubbles\streams\memory\MemoryInputStream` and
`stubbles\streams\memory\MemoryOutputStream` exist. While the memory input
stream class expects the content to be read from as string parameter for its
constructor, the memory output stream does not expect a value on construction,
but offers an additional method `buffer()` which returns all data written to
this stream so far. Additionally, casting the `stubbles\streams\memory\MemoryOutputStream`
to string will return the buffer (_available since release 4.0.0_).

The memory input stream is a seekable stream.


Filter streams
--------------

When reading from or writing data to a stream sometimes it may happen that not
all data to read or not all data to be written is relevant for what you want to
achieve. For example, a file you read may contain comment lines, and you don't
want to ignore those comment lines. Normally, you would have the logic on what
to ignore in your reading class:

```php
while (!$inputStream->eof()) {
    $line = $inputStream->readLine();
    if (substr(0, 1, $line) !== '#') {
        $this->processLine($line);
    }
}
```

In this small example it may not be much work, but what if comment lines may
also start with `//` or there may even be comments stretching over more than one
line as we have in PHP with `/* comment over several lines ... */`. Now the
logic might get a bit to complicated at this point. Stream filters to the rescue:


```php
$filterStream = new FilteredInputStream(
        $inputStream,
        function($line) { return substr($line, 0, 1) !== '#'; }
);
while (!$filterStream->eof()) {
    $this->processLine($inputStream->readLine());
}
```

The second argument for `stubbles\streams\filter\FilteredInputStream` can be any
`callable` which accepts a string as argument and returns `true` when this string
should be passed through, and `false` when this string should be filtered. Please
note that you can't filter single characters from the passed string, only
characters as a whole.

The same applies to the `stubbles\streams\filter\FilteredOutputStream`,
just that the filter is applied to the data that is written.


Input stream sequences
----------------------

Because iterating over input streams with `while` can be quite cumbersome,
_stubbles/streams_  provides an input stream implementation which is also an
instance of `\Iterator`.

```php
$lines = new InputStreamIterator(new FileInputStream('somefile.txt'));
foreach ($lines as $line {
    processLine($line);
}
```

Each iteration step is a call to `readLine()` of the decorated input stream.

Please note: when the decorated stream is not an instance of `stubbles\streams\Seekable`
it can be iterated only once, trying to rewind will do nothing.


Integration with _stubbles/sequence_
------------------------------------

Optionally iteration can be even more enhanced when the package
_[stubbles/sequence](https://github.com/stubbles/stubbles-sequence)_ is available.

This allows to use two functions which return an instance of
`stubbles\sequence\Sequence` that allow all sequence operation on an input
stream:

### `stubbles\streams\linesOf($input)`

The input can either be an instance of `stubbles\streams\InputStream` or a file
name.

```php
$lines = linesOf('somefile.txt')
        ->filter(/* callable which filters */)
        ->map(/* callable which maps the line to other content */);
foreach ($lines as $line) {
    processLine($line);
}
```

### `stubbles\streams\nonEmptyLinesOf($input)`

Same as above, but already filters all empty lines.
