7.0.0 (2016-??-??)
------------------

  * split off from [stubbles/core](https://github.com/stubbles/stubbles-core)


### BC breaks

  * all methods in `stubbles\streams\*` which threw `stubbles\lang\exception\IOException` now throw `stubbles\streams\StreamException`

### Other changes

  * removed seeking restrictions on `stubbles\streams\StandardInputStream`