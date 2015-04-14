# InputStream Interface #
Implemented by Interface `Curly_Stream_Input`

## Methods ##
`public boolean available()`

Checks if more data is available in this stream.

`public string read(integer len)`

Reads data out of this stream.

`public void skip(integer len)`

Skips data of this stream.

## Example usage ##
```
// Usage for every stream implementing this interface
$stream=CreateStream();
$buffer='';

// We don't want the first 10 bytes
$stream->skip(10);

// Read the rest of the stream into the buffer
while($stream->available()) {
	$buffer.=$stream->read(1024);
}
```