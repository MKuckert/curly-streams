<?php

/**
 * Curly_Phar_Compiler
 * 
 * Erstellt eine Phar-Archiv-Datei
 * 
 * @author Martin Kuckert
 * @copyright Copyright (c) 2009 Martin Kuckert
 * @license New BSD License
 * @package Curly.Phar
 * @since 19.11.2009
 * @todo Translate to english
 */
class Curly_Phar_Compiler {
	
	/**
	 * @var Curly_Phar_Configuration Die zu verwendende Konfiguration.
	 */
	private $_config=NULL;
	
	/**
	 * @var Phar Die aktuell verwendete Phar-Instanz.
	 */
	private $phar=NULL;
	
	private $signatureMap=array(
		Curly_Phar_Configuration::SIGNATURE_MD5		=> Phar::MD5,
		Curly_Phar_Configuration::SIGNATURE_SHA1		=> Phar::SHA1,
		Curly_Phar_Configuration::SIGNATURE_SHA256	=> Phar::SHA256,
		Curly_Phar_Configuration::SIGNATURE_SHA512	=> Phar::SHA512
	);
	
	/**
	 * Gibt die zu verwendende Konfiguration zurück.
	 * 
	 * @return Curly_Phar_Configuration
	 */
	public function getConfiguration() {
		return $this->_config;
	}
	
	/**
	 * Setzt die zu verwendende Konfiguration.
	 * 
	 * @return Curly_Phar_Action
	 * @param Curly_Phar_Configuration
	 */
	public function setConfiguration(Curly_Phar_Configuration $value) {
		$this->_config=$value;
		return $this;
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param Curly_Phar_Configuration
	 */
	public function __construct(Curly_Phar_Configuration $config) {
		$this->setConfiguration($config);
	}
	
	/**
	 * Führt die Archiverzeugung durch.
	 * 
	 * @return boolean
	 */
	public function execute() {
		$config=$this->getConfiguration();
		
		$targetPath=$config->getTargetPath();
		
		// Die Phar-Klasse mag leere Dateien nicht
		if(is_file($targetPath) and filesize($targetPath)<=0) {
			unlink($targetPath);
		}
		
		$this->phar=new Phar($targetPath);
		$this->phar->setSignatureAlgorithm($this->signatureMap[$config->getSignature()]);
		$this->phar->buildFromDirectory($config->getSourceDirectory(), '/\.php$/i');
		$this->phar->setDefaultStub();
		
		return true;
	}
	
}