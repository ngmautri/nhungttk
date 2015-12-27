<?php

namespace Inventory\Services;
use NMT\Files;

class AssetService{


	/**
	 *
	 * @param $name
	 * @return unknown_type
	 */
	public function createAssetFolderById($id)
	{
		try
		{
			$asset_folders_tpl = ROOT.
			DIRECTORY_SEPARATOR ."data".
			DIRECTORY_SEPARATOR ."templates".
			DIRECTORY_SEPARATOR ."asset_folder";

			$asset_dir = ROOT.
			DIRECTORY_SEPARATOR ."data".
			DIRECTORY_SEPARATOR ."assets".
			DIRECTORY_SEPARATOR ."asset_" . $id;
			
			var_dump($asset_dir);

			if(is_dir($asset_dir))
			{
				Files::recursiveCopy($asset_folders_tpl,$asset_dir);
				return true;

			}else{
				if(mkdir($asset_dir))
				{
					Files::recursiveCopy($asset_folders_tpl,$asset_dir);
					return true;
				}
			}
			return false;

		}catch(Exception $e)
		{
			
		}
	}
}