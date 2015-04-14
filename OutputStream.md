# OutputStream Interface #
Implemented by Interface `Curly_Stream_Output`

## Methods ##
`public void flush()`

Writes any buffered data into the stream.

`public void write(string data)`

Writes the given elements into the stream.

## Example usage ##
```
// Usage for every stream implementing this interface
$stream=CreateStream();
$data=implode('', range(0, 100));

// Write the data in 1k chunks into the stream.
while(strlen($data)>0) {
	$stream->write(substr($data, 0, 1024));
	$data=substr($data, 1024);
}
```