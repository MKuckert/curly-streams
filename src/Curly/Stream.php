<?php

/**
 * Curly_Stream
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009-2010 Martin Kuckert
 * @license New BSD License
 * @package Curly
 * @since 11.09.2009
 */
interface Curly_Stream extends Curly_Stream_Input, Curly_Stream_Output {
	
	/**#@+
	 * @desc Possible endian values
	 */
	const ENDIAN_LITTLE=true;
	const ENDIAN_BIG=false;
	/**#@-*/
	
}