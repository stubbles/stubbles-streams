8.0.0 (2016-07-??)
------------------

### BC breaks

  * raised minimum required PHP version to 7.0.0
  * introduced scalar type hints and strict type checking
  * deprecated `stubbles\streams\DecodingInputStream::getCharset()`, use `stubbles\streams\DecodingInputStream::charset()` instead, will be removed with 9.0.0
  * deprecated `stubbles\streams\EncodingOutputStream::getCharset()`, use `stubbles\streams\EncodingOutputStream::charset()` instead, will be removed with 9.0.0
  * deprecated `stubbles\streams\AbstractDecoratedInputStream`, use `stubbles\streams\DecoratedInputStream` instead, will be removed with 9.0.0
  * deprecated `stubbles\streams\AbstractDecoratedOutputStream`, use `stubbles\streams\DecoratedOutputStream` instead, will be removed with 9.0.0


  ### Other changes

 * fixed `stubbles\streams\InputStreamIterator` swalling the last line
 * fixed `stubbles\streams\file\FileInputStream::bytesLeft()` returning void when created with filename but not prefixed with _compress.*://_


7.0.0 (2016-01-11)
------------------

  * split off from [stubbles/core](https://github.com/stubbles/stubbles-core)


### BC breaks

  * all methods in `stubbles\streams\*` which threw `stubbles\lang\exception\IOException` now throw `stubbles\streams\StreamException`

### Other changes

  * removed seeking restrictions on `stubbles\streams\StandardInputStream`
