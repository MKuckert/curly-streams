# SeekableStream Interface #
Implemented in `Curly_Stream_Seekable`

Extends a datastream by a seek operation, so a certain position can directly being reached.

## Methods ##
`public void seek(integer offset, integer origin=ORIGIN_CURRENT)`

Seeks to a specified position in a datastream.

Possible values for the origin argument is any of the ORIGIN-constants of the interface. There meanings are as following:
  * `ORIGIN_BEGIN`: Seeks from the beginning of the stream
  * `ORIGIN_CURRENT`: Seeks from the current position
  * `ORIGIN_END`: Seeks from the end of the stream. offset has to be negative.

`public integer tell()`

Returns the current offset to the beginning of the datastream.