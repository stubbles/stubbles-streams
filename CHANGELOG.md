# Changelog

## 10.0.0 (2023-12-31)

### BC breaks

* raised minimum required PHP version to 8.2
* `stubbles\streams\ResourceInputStream::read()`, `stubbles\streams\ResourceInputStream::readLine()`, `stubbles\streams\ResourceOutputStream::write()`, `stubbles\streams\StandardInputStream::seek()`, `stubbles\streams\StandardInputStream::tell()`, `stubbles\streams\file\FileInputStream::seek()` and `stubbles\streams\file\FileInputStream::tell()` now throw a `\LogicException` when the underlying resource was closed instead of a `stubbles\streams\StreamException`

## 9.1.0 (2019-12-11)

* both `stubbles\streams\StandardInputStream` and `stubbles\streams\file\FileInputStream` will now throw a `stubbles\streams\StreamException` when seeking fails
* added more phpstan related type hints

## 9.0.0 (2019-11-08)

### BC breaks

* raised minimum required PHP version to 7.3
* removed methods and classes deprecated with 8.0.0
  * `stubbles\streams\DecodingInputStream::getCharset()`, use `stubbles\streams\DecodingInputStream::charset()` instead
  * `stubbles\streams\EncodingOutputStream::getCharset()`, use `stubbles\streams\EncodingOutputStream::charset()` instead
  * `stubbles\streams\AbstractDecoratedInputStream`, use `stubbles\streams\DecoratedInputStream` instead
  * `stubbles\streams\AbstractDecoratedOutputStream`, use `stubbles\streams\DecoratedOutputStream` instead

### Other changes

* `stubbles\streams\DecodingInputStream` now accepts a third parameter so decoding charset can be set to something else than UTF-8
* `stubbles\streams\EncodingOutputStream` now accepts a third parameter so charset to encode from can be set to something else than UTF-8

## 8.1.0 (2016-08-30)

* implemented #1 new function to copy a complete input stream to an output stream: added `stubbles\streams\copy()`

## 8.0.0 (2016-07-20)

### BC breaks

* raised minimum required PHP version to 7.0.0
* introduced scalar type hints and strict type checking
* deprecated `stubbles\streams\DecodingInputStream::getCharset()`, use `stubbles\streams\DecodingInputStream::charset()` instead, will be removed with 9.0.0
* deprecated `stubbles\streams\EncodingOutputStream::getCharset()`, use `stubbles\streams\EncodingOutputStream::charset()` instead, will be removed with 9.0.0
* deprecated `stubbles\streams\AbstractDecoratedInputStream`, use `stubbles\streams\DecoratedInputStream` instead, will be removed with 9.0.0
* deprecated `stubbles\streams\AbstractDecoratedOutputStream`, use `stubbles\streams\DecoratedOutputStream` instead, will be removed with 9.0.0

### Other changes

* fixed `stubbles\streams\InputStreamIterator` swallowing the last line
* fixed `stubbles\streams\file\FileInputStream::bytesLeft()` returning void when created with filename but not prefixed with _compress.*://_

## 7.0.0 (2016-01-11)

* split off from [stubbles/core](https://github.com/stubbles/stubbles-core)

### BC breaks

* all methods in `stubbles\streams\*` which threw `stubbles\lang\exception\IOException` now throw `stubbles\streams\StreamException`

### Other changes

* removed seeking restrictions on `stubbles\streams\StandardInputStream`
