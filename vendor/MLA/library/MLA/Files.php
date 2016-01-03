<?php

namespace MLA;


/**
 *
 * @author Ngmautri
 *
 */
class Files
{
	// ~ Attribute =========================================================================

	/**
	 *
	 * @param $from
	 * @param $to
	 * @return unknown_type
	 */
	public static function recursiveCopy($from,$to)
	{
		if ($gestor = opendir($from)) {
			while (false !== ($archivo = readdir($gestor))) {
				if ($archivo{0} != ".") {
					if(!is_dir($from.'/'.$archivo)) {
						copy($from.'/'.$archivo,$to.'/'.$archivo);
					} else {
						mkdir($to.'/'.$archivo);
						self::recursiveCopy($from.'/'.$archivo,$to.'/'.$archivo);
					}
				}
			}
			closedir($gestor);
		}
	}

	/**
	 *
	 * @param $email
	 * @return unknown_type
	 */
	public static function getAcronim($input)
	{
		$userLenght = strlen($input);
		//Generate de dir, remove $userDir ? maybe.
		return $input{0}.$input{$userLenght-1}.substr($userLenght,-1);
	}

	/**
	 * 
	 * @param $dir
	 * @return unknown_type
	 */
	public static function deleteDirectory($dir) {
		//echo($dir);
		if (!file_exists($dir)) return true;
		if (!is_dir($dir)) return unlink($dir);
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') continue;
			if (!self::deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) return false;
		}
		return rmdir($dir);
	}
}