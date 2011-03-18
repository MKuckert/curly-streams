<?php

/**
 * Curly_Phar_Configuration
 * 
 * Kapselt die Konfigurationen für die Erstellung eines Phar-Archives.
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Phar
 * @since 19.11.2009
 * @todo Translate to english
 */
class Curly_Phar_Configuration {
	
	/**#@+
	 * @desc Mögliches Archiv-Format
	 */
	const FORMAT_PHAR=0;
	const FORMAT_TAR=1;
	const FORMAT_ZIP=2;
	/**#@-*/
	
	/**#@+
	 * @desc Mögliche Komprimierung
	 */
	const COMPRESS_NONE=0;
	const COMPRESS_GZ=1;
	const COMPRESS_BZIP2=2;
	/**#@-*/
	
	/**#@+
	 * @desc Mögliche Signaturalgorithmen
	 */
	const SIGNATURE_MD5=0;
	const SIGNATURE_SHA1=1;
	const SIGNATURE_SHA256=2;
	const SIGNATURE_SHA512=3;
	/**#@-*/
	
	/**
	 * @var boolean Flag, ob diese Instanz geändert werden darf.
	 */
	private $_mutable=true;
	
	/**
	 * @var string Das Quellverzeichnis für das Archiv.
	 */
	private $_sourceDirectory=NULL;
	
	/**
	 * @var string Dateipfad zum Archiv.
	 */
	private $_targetPath=NULL;
	
	/**
	 * @var integer Das Format des Archivs.
	 */
	private $_format=self::FORMAT_PHAR;
	
	/**
	 * @var integer Das Komprimierungsverfahren des Archivs.
	 */
	private $_compression=self::COMPRESS_NONE;
	
	/**
	 * @var integer Der Signaturalgorithmus des Archivs.
	 */
	private $_signature=self::SIGNATURE_MD5;
	
	/**
	 * @var boolean Flag, ob eine eigene Stub-Datei verwendet werden soll.
	 */
	private $_useStub=false;
	
	/**
	 * @var string Dateipfad zu der Stub-Datei.
	 */
	private $_stubPath=NULL;
	
	/**
	 * Gibt das Quellverzeichnis für das Archiv zurück.
	 * 
	 * @return string
	 */
	public function getSourceDirectory() {
		return $this->_sourceDirectory;
	}
	
	/**
	 * Setzt das Quellverzeichnis für das Archiv.
	 * 
	 * @throws Curly_Phar_Exception
	 * @return Curly_Phar_Configuration
	 * @param string
	 */
	public function setSourceDirectory($value) {
		$this->ensureMutable();
		$this->_sourceDirectory=(string)$value;
		if(substr($this->_sourceDirectory, 0, 8)=='file:///') {
			$this->_sourceDirectory=substr($this->_sourceDirectory, 8);
		}
		return $this;
	}
	
	/**
	 * Gibt den Dateipfad zum Archiv zurück.
	 * 
	 * @return string
	 */
	public function getTargetPath() {
		return $this->_targetPath;
	}
	
	/**
	 * Setzt den Dateipfad zum Archiv.
	 * 
	 * @throws Curly_Phar_Exception
	 * @return Curly_Phar_Configuration
	 * @param string
	 */
	public function setTargetPath($value) {
		$this->ensureMutable();
		$this->_targetPath=(string)$value;
		if(substr($this->_targetPath, 0, 8)=='file:///') {
			$this->_targetPath=substr($this->_targetPath, 8);
		}
		return $this;
	}
	
	/**
	 * Gibt das Format des Archivs zurück.
	 * 
	 * @return integer
	 */
	public function getFormat() {
		return $this->_format;
	}
	
	/**
	 * Legt das Format des Archivs fest.
	 * 
	 * @throws Curly_Phar_Exception
	 * @return Curly_Phar_Configuration
	 * @param integer
	 */
	public function setFormat($value) {
		$this->ensureMutable();
		if($value===self::FORMAT_PHAR or $value===self::FORMAT_TAR or $value===self::FORMAT_ZIP) {
			$this->_format=$value;
		}
		else {
			throw new Curly_Phar_Exception('The given value is no valid format');
		}
		return $this;
	}
	
	/**
	 * Gibt das Komprimierungsverfahren des Archivs zurück.
	 * 
	 * @return integer
	 */
	public function getCompression() {
		return $this->_compression;
	}
	
	/**
	 * Setzt das Komprimierungsverfahren des Archivs.
	 * 
	 * @throws Curly_Phar_Exception
	 * @return Curly_Phar_Configuration
	 * @param integer
	 */
	public function setCompression($value) {
		$this->ensureMutable();
		if($value===self::COMPRESS_NONE or $value===self::COMPRESS_GZ or $value===self::COMPRESS_BZIP2) {
			$this->_compression=$value;
		}
		else {
			throw new Curly_Phar_Exception('The given value is no valid compression');
		}
		return $this;
	}
	
	/**
	 * Gibt den Signaturalgorithmus des Archivs zurück.
	 * 
	 * @return integer
	 */
	public function getSignature() {
		return $this->_signature;
	}
	
	/**
	 * Setzt den Signaturalgorithmus des Archivs.
	 * 
	 * @throws Curly_Phar_Exception
	 * @return Curly_Phar_Configuration
	 * @param integer
	 */
	public function setSignature($value) {
		$this->ensureMutable();
		if($value===self::SIGNATURE_MD5 or $value==self::SIGNATURE_SHA1 or $value==self::SIGNATURE_SHA256 or $value==self::SIGNATURE_SHA512) {
			$this->_signature=$value;
		}
		else {
			throw new Curly_Phar_Exception('The given value is no valid signature');
		}
		return $this;
	}
	
	/**
	 * Gibt das Flag, ob eine eigene Stub-Datei verwendet werden soll, zurück.
	 * 
	 * @return boolean
	 */
	public function getUseStub() {
		return $this->_useStub;
	}
	
	/**
	 * Setzt das Flag, ob eine eigene Stub-Datei verwendet werden soll.
	 * 
	 * @throws Curly_Phar_Exception
	 * @return Curly_Phar_Configuration
	 * @param boolean
	 */
	public function setUseStub($value) {
		$this->ensureMutable();
		$this->_useStub=Curly_Types_Ensure::boolean($value);
		return $this;
	}
	
	/**
	 * Gibt den Dateipfad zu der Stub-Datei zurück.
	 * 
	 * @return string
	 */
	public function getStubPath() {
		return $this->_stubPath;
	}
	
	/**
	 * Setzt den Dateipfad zu der Stub-Datei.
	 * 
	 * @throws Curly_Phar_Exception
	 * @return Curly_Phar_Configuration
	 * @param string
	 */
	public function setStubPath($value) {
		$this->ensureMutable();
		$this->_stubPath=$value;
		return $this;
	}
	
	/**
	 * Überprüft, ob diese Instanz geändert werden darf und wirft eine Ausnahme
	 * wenn dies nicht der Fall ist.
	 * 
	 * @throws Curly_Phar_Exception
	 * @return void
	 */
	public function ensureMutable() {
		if(!$this->_mutable) {
			throw new Curly_Phar_Exception('This instance is immutable');
		}
	}
	
	/**
	 * Überprüft, ob diese Instanz geändert werden darf.
	 * 
	 * @return boolean
	 */
	public function isMutable() {
		return $this->_mutable;
	}
	
	/**
	 * Friert den aktuellen Zustand dieser Instanz ein.
	 * 
	 * @return Curly_Phar_Configuration
	 */
	public function freeze() {
		$this->_mutable=false;
		return $this;
	}
	
}