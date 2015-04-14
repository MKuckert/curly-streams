# BufferedOutputStream #
Implemented by Class `Curly_Stream_Buffered_Output`

This class is a wrapper for another output stream to use an internal buffer and reduce the number of attempted write operations. This is very efficient when performing many write operations on a slow OutputStream, e.g. a FileOutputStream.

## Implements ##
  * OutputStream (`Curly_Stream_Output`)

## Example usage ##
```
$stream=new Curly_Stream_Buffered_Output(
	new Curly_Stream_File_Output('/path/to/file.txt', Curly_Stream_File::CREATE | Curly_Stream_File::OPEN)
);

// Write the data in small chunks
for($i=0; $i<1000; $i++) {
	$stream->write($i);
}
```