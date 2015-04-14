# AppendInputStream #
Implemented by class `Curly_Stream_Append_Input`

Combines any number of InputStream objects to one stream.

## Implements ##
  * InputStream (`Curly_Stream_Input`)

## Example usage ##
```
$stream=new Curly_Stream_Append_Input();
$stream
	->append(new Curly_Stream_File_Input('path/to/file.txt'))
	->append(new Curly_Stream_Memory_Input('Stuff from memory'))
	->append(new Curly_Stream_File_Input('path/to/second-file.txt'))
;

$bufSize=1024;
while($stream->available()) {
	doAnyStuffWithData($stream->read($bufSize));
}
```