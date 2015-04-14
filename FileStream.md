# FileStream #
Implemented by Class `Curly_Stream_File`

This class combines the FileInputStream and FileOutputStream classes in one interface to perform read and write operations with one stream.

## Implements ##
  * InputStream (`Curly_Stream_Input`)
  * OutputStream (`Curly_Stream_Output`)
  * SeekableStream (`Curly_Stream_Seekable`)

## Open modes ##
You can pass different opening modes to the constructor of this class (and to the FileOutputStream class) as a bitflag. Possible modes are the following:
  * `OPEN`: Opens the file under the given path. If it does not exist and the CREATE flag is not set an exception is thrown.
  * `CREATE`: Creates the file under the given path. If it does already exist and the OPEN flag is not set an exception is thrown.
  * `TRUNCATE`: The file is truncated during opening. This can be used together with the CREATE or OPEN flags.
  * `CLEAN`: Opens an existing file after truncating it or creates a new file. This flag is just a combination of TRUNCATE, OPEN and CREATE for convenience.

## Example usage ##
```
$path='/path/to/file.txt';
$stream=new Curly_Stream_File($path, Curly_Stream_File::OPEN); // OPEN: The file has to exist
$stream=new Curly_Stream_File($path, Curly_Stream_File::CREATE); // CREATE: The file is newly created
$stream=new Curly_Stream_File($path, Curly_Stream_File::CREATE|Curly_Stream_File::OPEN); // OPEN | CREATE: The file is newly created or opened if it already exists

// Do any write operations
$stream->write($data);

// Read operations are also possible
while($stream->available()) {
	$data=$stream->read(1024);
}

// This class implements the Seekable interface
$stream->write('123');
$stream->seek(0, Curly_Stream_Seekable::ORIGIN_BEGIN);
$stream->read(3); // =123
```