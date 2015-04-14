# FileOutputStream #
Implemented by Class `Curly_Stream_File_Output`

This class implements write functionality for files. The default php file functions are used.

## Implements ##
  * OutputStream (`Curly_Stream_Output`)
  * SeekableStream (`Curly_Stream_Seekable`)

## Example usage ##
```
$stream=new Curly_Stream_File_Output('/path/to/file.txt', Curly_Stream_File::OPEN);
$stream->write($data);
```

See FileStream for the possible open modes passable to the constructor.