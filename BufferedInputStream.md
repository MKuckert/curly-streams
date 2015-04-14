# BufferedInputStream #
Implemented by Class `Curly_Stream_Buffered_Input`

This class is a wrapper for another stream to use an internal buffer and reduce the number of attempted read operations. This is very efficient when performing many read operations on a slow InputStream, e.g. a FileInputStream.

## Implements ##
  * InputStream (`Curly_Stream_Input`)

## Example usage ##
```
$stream=new Curly_Stream_Buffered_Input(
	new Curly_Stream_File_Input('/path/to/file.txt')
);

// Read the data in small chunks
while($stream->available()) {
	$data=$stream->read(4);
}
```