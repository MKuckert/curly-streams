# BinaryInputStream #
Implemented by Interface `Curly_Stream_Binary_Input`

## Implements ##
  * CapsuleInputStream (`Curly_Stream_Capsule_Input`)

## Methods (additionally to InputStream) ##
`public boolean getUseLittleEndian()`

Checks whether this reader uses little endian byte order.

`public Curly_Stream_Binary_Reader setUseLittleEndian(boolean flag=true)`

Sets the flag to use little endian byte order or removes it.

`public integer readByte()`

Reads a single byte from the underlying stream.

`public integer readShort()`

Reads a 16 bit unsigned short from the underlying stream.

`public integer readInteger()`

Reads a 32 bit unsigned integer from the underlying stream.

## Example usage ##
```
$stream=new Curly_Stream_Binary_Input(
	new Curly_Stream_Buffered_Input(
		new Curly_Stream_File_Input(
			'/path/to/file.txt'
		)
	)
, Curly_Stream_Binary_Input::ENDIAN_BIG);

$stream->readByte();
$stream->readShort();
$stream->readInteger();
```