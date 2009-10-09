<?php

require_once 'util.php';

$DIR=dirname(__FILE__).'/../src/Stream/';
$MDIR=$DIR.'Memory/';

$head=<<<HEAD
<?php

/**
 * Curly_Stream_Memory
 * 
 * Implements an stream with data hold in memory.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Memory
 * @since 12.09.2009
 */
class Curly_Stream_Memory extends Curly_Stream_Memory_Seekable implements Curly_Stream {

HEAD;
$foot=<<<FOOT
	
}
FOOT;

$input=readStreamFile($MDIR.'Input.php');
// Make constructor argument optional
$input=str_replace('__construct($data)', "__construct(\$data='')", $input);
$output=readStreamFile($MDIR.'Output.php');

$file=$head.$input.$output.$foot;
file_put_contents($DIR.'Memory.php', $file);
