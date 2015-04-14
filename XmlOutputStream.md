# XmlOutputStream #
Implemented by class `Curly_Stream_Xml_Output`

Provides write operations to generate xml data. This stream requires another stream to do the real output stuff.

## Implements ##
  * OutputStream (`Curly_Stream_Output`)

## Methods ##
`public Curly_Stream_Output getInnerStream()`

Returns the capsuled output stream.

`public string getCharset()`

Returns the charset to use.

`public Curly_Stream_Xml_Output setCharset(string value)`

Sets the charset to use for the XML head and the escape method.

`public __construct(Curly_Stream_Output stream, string charset=DEFAULT_CHARSET)`

Constructor. Sets the output stream to write the xml data to and the charset to use for the XML head and the escape method. The default value is UTF-8.

`public void flush()`

Writes any buffered data into the stream.

`public string escape(string, boolean replaceQuotes=false)`

Escapes the given data as valid xml.

The chars &, < and > and the " if the quotes argument is set.

`public void write(string data)`

Writes the given elements into the stream.

`public Curly_Stream_Xml_Output writeCData(string data)`

Writes the given elements as a cdata tag into the stream.

`public Curly_Stream_Xml_Output writeEntity(string name)`

Writes an entity with the given name into the stream.

`public Curly_Stream_Xml_Output writeHeadDeclaration(boolean standalone=false)`

Writes the xml head declaration.

`public Curly_Stream_Xml_Output addAttribute(string name, string value)`

Adds an attribute to the list, so it's written with the next opening or empty tag.

`public Curly_Stream_Xml_Output addAttributes(array attributes)`

Adds a list of attributes to the list, so they are written with the next opening or empty tag.

`public Curly_Stream_Xml_Output writeStartTag(string name)`

Writes the begin of a non-empty tag.

`public Curly_Stream_Xml_Output writeEndTag()`

Writes an end tag with the name of the last opened start tag.

`public Curly_Stream_Xml_Output writeTag(string name)`

Writes an empty tag.

`public Curly_Stream_Xml_Output writeProcessingInstruction(string name, string body)`

Writes an processing instruction.

`public Curly_Stream_Xml_Output writeComment(string data)`

Writes a comment.

## Example usage ##
```
$stream=new Curly_Stream_Xml_Output(
	new Curly_Stream_File_Output('/path/to/out.xml', Curly_Stream_File::CLEAN)
);
$stream
	->writeHeadDeclaration()
	->writeStartTag('root')
	->writeStartTag('data')
	->write('DATA')
	->writeEndTag();

for($i=0; $i<3; $i++) {
	$stream
		->addAttribute('value', $i)
		->writeTag('i');
}

$stream->writeEndTag();
```

Creates the following xml data:

```
<?xml version="1.0" encoding="UTF-8" ?>
<root><data>DATA</data><i value="0" /><i value="1" /><i value="2" /></root>
```