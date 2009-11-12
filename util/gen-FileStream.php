<?php

require_once 'util.php';

$DIR=dirname(__FILE__).'/../src/Stream/';
$MDIR=$DIR.'File/';

$head=<<<HEAD
<?php

/**
 * Curly_Stream_File
 * 
 * Implements an stream to read and write data to a file.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.File
 * @since 12.09.2009
 */
class Curly_Stream_File extends Curly_Stream_File_Seekable implements Curly_Stream {
	
	/**
	 * @desc Truncates the underlying file before opening the stream.
	 */
	const TRUNCATE=1;
	
	/**
	 * @desc Creates the file, if it doesn´t exist yet.
	 */
	const CREATE=2;
	
	/**
	 * @desc Opens an existing file.
	 */
	const OPEN=4;
	
	/**
	 * @desc Opens an existing file after truncating it or creates a new file.
	 * Combination of TRUNCATE, OPEN and CREATE just for convenience.
	 */
	const CLEAN=7;
	
	/**
	 * Constructor
	 * 
	 * @throws Curly_Stream_Exception
	 * @param string Path to the file
	 * @param integer Opening modes. Default is CREATE|OPEN
	 */
	public function __construct(\$filepath, \$mode=NULL) {
		if(\$mode===NULL) {
			\$mode=Curly_Stream_File::CREATE|Curly_Stream_File::OPEN;
		}
		
		\$filepath=(string)\$filepath;
		if(is_numeric(\$mode)) {
			\$mode=(int)\$mode;
			if((\$mode & Curly_Stream_File::OPEN)===Curly_Stream_File::OPEN) {
				// File does not exist
				if((\$mode & Curly_Stream_File::CREATE)!==Curly_Stream_File::CREATE and !file_exists(\$filepath)) {
					throw new Curly_Stream_Exception('The file '.\$filepath.' does not exist and can not been opened');
				}
				
				if((\$mode & Curly_Stream_File::TRUNCATE)===Curly_Stream_File::TRUNCATE) {
					\$mode='w+';
				}
				else {
					\$mode='a+';
				}
			}
			else if((\$mode & Curly_Stream_File::CREATE)===Curly_Stream_File::CREATE) {
				\$mode='x+';
			}
			else {
				throw new Curly_Stream_Exception('Invalid open mode given. At least the open or create mode has to be specified');
			}
		}
		else {
			\$mode=trim(\$mode, 'b');
		}
		
		\$this->_handle=fopen(\$filepath, \$mode.'b');
		if(!is_resource(\$this->_handle)) {
			throw new Curly_Stream_Exception('Failed to open an stream to the file '.\$filepath);
		}
	}

HEAD;
$foot=<<<FOOT
	
}
FOOT;

$input=readFStreamFile($MDIR.'Input.php');
$output=readFStreamFile($MDIR.'Output.php');

$file=$head.$input.$output.$foot;
file_put_contents($DIR.'File.php', $file);

function readFStreamFile($file) {
	$in=readStreamFile($file, true);
	
	// Skip ctr
	reset($in);
	$i=key($in);
	$c=count($in)+$i;
	for(; $i<$c; $i++) {
		$l=$in[$i];
		unset($in[$i]);
		if(strpos($l, '// end of ctr')!==false) {
			break;
		}
	}
	
	return rtrim(implode('', $in)).PHP_EOL;
}