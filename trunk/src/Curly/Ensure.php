<?php

/**
 * Curly_Types_Ensure
 * F�hrt Typenkonversionen durch, um in Methoden sicherzustellen, das alle
 * Variablen vom korrekten Typ sind.
 *
 * Folgende Typen werden in den Dokumentationen zu den einzelnen Methoden
 * dieser Klasse verwendet:
 *  - Boolean
 *  - Integer
 *  - Double
 *  - String
 *  - Array
 *  - Object
 *  - Resource
 *  - NULL
 *  - unbekannter Typ
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 30.04.2008
 * @version 1.0
 */
final class Curly_Types_Ensure {

	/**
	 * @var array Intern f�r die {@link type}-Methode verwendetes Mapping.
	 */
	private $_typeMapping = array(
		'i'						=> 'int',
		'integer'				=> 'int',
		'short'					=> 'int',
		'u'						=> 'uint',
		'uinteger'				=> 'uint',
		'unsigned int'			=> 'uint',
		'unsigned integer'		=> 'uint',
		'b'						=> 'boolean',
		'bool'					=> 'boolean',
		'd'						=> 'double',
		'f'						=> 'double',
		'float'					=> 'double',
		'l'						=> 'double',
		'large'					=> 'double',
		'large int'				=> 'double',
		'large integer'			=> 'double',
		'lint'					=> 'double',
		's'						=> 'string',
		'str'					=> 'string',
		'c'						=> 'string',
		'char'					=> 'string',
		'a'						=> 'array',
		'ar'					=> 'array',
		'arr'					=> 'array',
		'o'						=> 'object',
		'obj'					=> 'object'
	);
	
	/**
	 * Diese Methode stellt sicher, dass aus dem �bergabewert ein Integer
	 * erstellt wird.
	 *
	 * F�r die Typen Boolean, Double, String und Resource und dem Wert NULL
	 * wird die {@link http://php.net/intval intval}-Funktion verwendet.
	 *
	 * Bei einem Array wird die Anzahl der Elemente im Array zur�ckgegeben.
	 *
	 * Bei einem Object wird die Anzahl der Elemente im Array der Funktion
	 * {@link http://php.net/get_object_vars get_object_vars}
	 * zur�ckgegeben.
	 *
	 * Bei einem unbekannten Typ wird 0 zur�ckgegeben.
	 *
	 * @return integer
	 * @param mixed
	 */
	static public function int( $value ) {
		switch( gettype( $value ) ) {
			case 'integer':
				return( $value );
			case 'boolean':
			case 'double':
			case 'string':
			case 'resource':
			case 'NULL':
				return( intval( $value ) );
			case 'array':
				return( count( $value ) );
			case 'object':
				return( count( get_object_vars( $value ) ) );
			default:
				return( 0 );
		}
	}

	/**
	 * Diese Methode stellt sicher, dass aus dem �bergabewert ein positiver
	 * Integer erstellt wird.
	 *
	 * Die R�ckgabe ist wie bei der Methode {@link int}, allerdings immer
	 * im positiven Wertebereich (inklusive Null).
	 *
	 * @return integer
	 * @param mixed
	 */
	static public function uint( $value ) {
		return( abs( self::int( $value ) ) );
	}

	/**
	 * Diese Methode stellt sicher, dass aus dem �bergabewert ein Integer
	 * erstellt wird.
	 *
	 * Die R�ckgabe ist wie bei der Methode {@link int}, allerdings wird
	 * bei einem Wert, der kleiner als $min ist, $min zur�ckgegeben bzw.
	 * wenn der Wert gr��er als $max ist wird $max zur�ckgegeben.
	 *
	 * @return integer
	 * @param mixed
	 * @param integer
	 * @param integer
	 */
	static public function range( $value, $min=0, $max=PHP_INT_MAX ) {
		$value = self::int( $value );
		if( $value<$min ) {
			return( $min );
		}
		else if( $value>$max ) {
			return( $max );
		}
		else {
			return( $value );
		}
	}

	/**
	 * Diese Methode stellt sicher, dass aus dem �bergabewert ein Array
	 * erstellt wird.
	 *
	 * F�r die Typen Boolean, Double, String und Resource wird ein Array
	 * zur�ckgegeben. Unter dem Index 0 ist der �bergabewert abgelegt.
	 *
	 * Bei einem Object wird ein Cast nach Array vorgenommen. D.h. es wird
	 * ein assoziatives Array mit allen Membern des Objektes zur�ckgegeben.
	 *
	 * Bei einem unbekannten Typ oder dem Wert NULL wird ein leeres Array
	 * zur�ckgegeben.
	 *
	 * @return array
	 * @param mixed
	 */
	static public function arr( $value ) {
		switch( gettype( $value ) ) {
			case 'boolean':
			case 'integer':
			case 'double':
			case 'string':
			case 'resource':
				return( array( $value ) );
			case 'array':
				return( $value );
			case 'object':
				if( $value instanceof Traversable ) {
					$retval = array();
					foreach( $value as $k=>$v ) {
						$retval[$k] = $v;
					}
					return $retval;
				}
				else if( method_exists( $value, 'toArray' ) ) {
					return $value->toArray();
				}
				else {
					return( (array)$value );
				}
			default:
				return( array() );
		}
	}

	/**
	 * Diese Methode stellt sicher, dass aus dem �bergabewert eine
	 * Zeichenkette erstellt wird.
	 *
	 * F�r die Typen Boolean, Double und Integer wird ein Cast nach String
	 * vorgenommen und zur�ckgegeben.
	 *
	 * Bei einem Array wird dieses mit einem Komma als Delimiter
	 * {@link http://php.net/implode implodiert} und zur�ckgegeben.
	 *
	 * Bei einem Objekt wird �berpr�ft, ob die Methode toString oder
	 * __toString vorhanden ist und dessen R�ckgabewert zur�ckgegeben.
	 * Ist keine dieser Methoden vorhanden, aber die Funktion
	 * spl_object_hash vorhanden, so wird dessen R�ckgabewert
	 * zur�ckgegeben.
	 * Als letzte M�glichkeit wird das Objekt serialisiert und dieser Wert
	 * zur�ckgegeben.
	 *
	 * Bei einer Resource wird der R�ckgabewert der Funktion
	 * {@link http://php.net/get_resource_type get_resource_type}
	 * zur�ckgegeben.
	 *
	 * Bei einem unbekannten Typ oder dem Wert NULL wird eine leere
	 * Zeichenkette zur�ckgegeben.
	 *
	 * @return string
	 * @param mixed
	 */
	static public function string( $value ) {
		switch( gettype( $value ) ) {
			case 'string':
				return( $value );
			case 'boolean':
			case 'integer':
			case 'double':
				return( strval( $value ) );
			case 'array':
				$value = array_map( array( __CLASS__, __FUNCTION__ ), $value );
				return( implode( ', ', $value ) );
			case 'object':
				if( method_exists( $value, 'toString' ) ) {
					return( $value->toString() );
				}
				else if( method_exists( $value, '__toString' ) ) {
					return( $value->__toString() );
				}
				else if( function_exists( 'spl_object_hash' ) ) {
					return( spl_object_hash( $value ) );
				}
				else {
					return( serialize( $value ) );
				}
			case 'resource':
				return( get_resource_type( $value ) );
			default:
				return( '' );
		}
	}

	/**
	 * Diese Methode stellt sicher, dass aus dem �bergabewert eine
	 * Gleitkommazahl erstellt wird.
	 *
	 * F�r die Typen Boolean, Integer, String und Resource und dem Wert NULL
	 * wird die {@link http://php.net/floatval floatval}-Funktion verwendet.
	 *
	 * Bei einem Array wird die Anzahl der Elemente im Array zur�ckgegeben.
	 *
	 * Bei einem Object wird die Anzahl der Elemente im Array der Funktion
	 * {@link http://php.net/get_object_vars get_object_vars}
	 * zur�ckgegeben.
	 *
	 * Bei einem unbekannten Typ wird 0 zur�ckgegeben.
	 *
	 * @return double
	 * @param mixed
	 */
	static public function double( $value ) {
		switch( gettype( $value ) ) {
			case 'double':
				return( $value );
			case 'boolean':
			case 'integer':
			case 'string':
			case 'resource':
			case 'NULL':
				return( floatval( $value ) );
			case 'array':
				return( floatval( count( $value ) ) );
			case 'object':
				return( floatval( count( get_object_vars( $value ) ) ) );
			default:
				return( 0.0 );
		}
	}

	/**
	 * Diese Methode stellt sicher, dass aus dem �bergabewert ein boolscher
	 * Wert erstellt wird.
	 *
	 * F�r die Typen Integer und Double wird true zur�ckgegeben, wenn der
	 * �bergabewert ungleich Null ist.
	 *
	 * Bei einem String wird false zur�ckgegeben, wenn diese Zeichenkette
	 * einer der folgenden Zeichenketten entspricht
	 * (ohne R�cksicht auf Gro�- und Kleinschreibung):
	 *  - no
	 *  - n
	 *  - false
	 *  - f
	 *  - off
	 *  - 0
	 *  - -
	 * In allen anderen F�llen wird true zur�ckgegeben.
	 *
	 * Bei einem Array wird true zur�ckgegeben, wenn dieses Elemente enth�lt.
	 *
	 * Bei einem Object oder einer Resource wird immer true zur�ckgegeben.
	 *
	 * Bei einem unbekannten Typ oder dem Wert NULL wird false zur�ckgegeben.
	 *
	 * @return double
	 * @param mixed
	 */
	static public function boolean( $value ) {
		switch( gettype( $value ) ) {
			case 'boolean':
				return( $value );
			case 'integer':
			case 'double':
				return( $value!=0 );
			case 'string':
				$value = strtolower( $value );
				return !(
					trim( $value ) == '' or 
					$value == 'no'		or
					$value == 'n'		or
					$value == 'false'	or
					$value == 'f'		or
					$value == 'off'		or
					$value == '0'		or
					$value == '-'
				);
			case 'array':
				return( !empty( $value ) );
			case 'object':
			case 'resource':
				return( true );
			default:
				return( false );
		}
	}
	
	/**
	 * Stellt sicher, dass der �bergebene Wert $value dem Typ $type entspricht.
	 * 
	 * @return mixed
	 * @param mixed
	 * @param string
	 */
	static public function type( $value, $type ) {
		$type = strtolower( trim( $type ) );
		if( isset( self::$_typeMapping[$type] ) ) {
			return( call_user_func_array( array( __CLASS__, self::$_typeMapping[$type] ), $value ) );
		}
		else if( class_exists( $type ) ) {
			return( new $type( $value ) );
		}
		else {
			return( null );
		}
	}
	
	/**
	 * Magische Methode f�r die {@link type}-Methode.
	 * 
	 * @return mixed
	 * @param string
	 * @param array
	 */
	static public function __callStatic( $method, $args ) {
		return( self::type( reset( $args ), $method ) );
	}

}

?>