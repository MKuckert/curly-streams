# FileInputStream #
Implemented by Class `Curly_Stream_File_Input`

For any reading file access this class is used. The default php file functions are used.

## Implements ##
  * InputStream (`Curly_Stream_Input`)
  * SeekableStream (`Curly_Stream_Seekable`)

## Example usage ##
```
$stream=new Curly_Stream_File_Input('/path/to/file.txt');
while($stream->available()) {
	$data=$stream->read(1024);
}

// Because of the native php functions all wrappers are possible with this stream
$stream=new Curly_Stream_File_Input('http://example.com/');

// This class implements the Seekable interface
$currentPosition=$stream->tell();
$stream->seek(0, Curly_Stream_Seekable::ORIGIN_BEGIN);
```