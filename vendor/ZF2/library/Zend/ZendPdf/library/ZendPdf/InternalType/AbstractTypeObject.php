<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */
namespace ZendPdf\InternalType;

use ZendPdf as Pdf;

/**
 * PDF file element implementation
 *
 * @package Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
abstract class AbstractTypeObject {
	const TYPE_BOOL = 1;
	const TYPE_NUMERIC = 2;
	const TYPE_STRING = 3;
	const TYPE_NAME = 4;
	const TYPE_ARRAY = 5;
	const TYPE_DICTIONARY = 6;
	const TYPE_STREAM = 7;
	const TYPE_NULL = 11;
	
	/**
	 * Reference to the top level indirect object, which contains this element.
	 *
	 * @var \ZendPdf\InternalType\IndirectObject
	 */
	private $_parentObject = null;
	
	/**
	 * Return type of the element.
	 * See ZPDFPDFConst for possible values
	 *
	 * @return integer
	 */
	abstract public function getType();
	
	/**
	 * Convert element to a string, which can be directly
	 * written to a PDF file.
	 *
	 * $factory parameter defines operation context.
	 *
	 * @param \ZendPdf\ObjectFactory $factory        	
	 * @return string
	 */
	abstract public function toString(Pdf\ObjectFactory $factory = null);
	const CLONE_MODE_SKIP_PAGES = 1; // Do not follow pages during deep copy process
	const CLONE_MODE_FORCE_CLONING = 2; // Force top level object cloning even it's already processed
	
	/**
	 * Detach PDF object from the factory (if applicable), clone it and attach to new factory.
	 *
	 * @todo It's necessary to check if SplObjectStorage class works faster
	 *       (Needs PHP 5.3.x to attach object _with_ additional data to storage)
	 *      
	 * @param \ZendPdf\ObjectFactory $factory
	 *        	The factory to attach
	 * @param
	 *        	array &$processed List of already processed indirect objects, used to avoid objects duplication
	 * @param integer $mode
	 *        	Cloning mode (defines filter for objects cloning)
	 * @return s \ZendPdf\InternalType\AbstractTypeObject
	 */
	public function makeClone(Pdf\ObjectFactory $factory, array &$processed, $mode) {
		return clone $this;
	}
	
	/**
	 * Set top level parent indirect object.
	 *
	 * @param \ZendPdf\InternalType\IndirectObject $parent        	
	 */
	public function setParentObject(IndirectObject $parent) {
		$this->_parentObject = $parent;
	}
	
	/**
	 * Get top level parent indirect object.
	 *
	 * @return \ZendPdf\InternalType\IndirectObject
	 */
	public function getParentObject() {
		return $this->_parentObject;
	}
	
	/**
	 * Mark object as modified, to include it into new PDF file segment.
	 *
	 * We don't automate this action to keep control on PDF update process.
	 * All new objects are treated as "modified" automatically.
	 */
	public function touch() {
		if ($this->_parentObject !== null) {
			$this->_parentObject->touch ();
		}
	}
	
	/**
	 * Clean up resources, used by object
	 */
	public function cleanUp() {
		// Do nothing
	}
	
	/**
	 * Convert PDF element to PHP type.
	 *
	 * @return mixed
	 */
	public function toPhp() {
		return $this->value;
	}
	
	/**
	 * Convert PHP value into PDF element.
	 *
	 * @param mixed $input        	
	 * @return \ZendPdf\InternalType\AbstractTypeObject
	 */
	public static function phpToPDF($input) {
		if (is_numeric ( $input )) {
			return new NumericObject ( $input );
		} elseif (is_bool ( $input )) {
			return new BooleanObject ( $input );
		} elseif (is_array ( $input )) {
			$pdfElementsArray = array ();
			$isDictionary = false;
			
			foreach ( $input as $key => $value ) {
				if (is_string ( $key )) {
					$isDictionary = true;
				}
				$pdfElementsArray [$key] = self::phpToPDF ( $value );
			}
			
			if ($isDictionary) {
				return new DictionaryObject ( $pdfElementsArray );
			} else {
				return new ArrayObject ( $pdfElementsArray );
			}
		} else {
			return new StringObject ( ( string ) $input );
		}
	}
}
