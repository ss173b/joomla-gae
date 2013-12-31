<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:52 PM
 */

namespace garyamort\github_io\stream\mapper;


class replaceStart extends replace {


	public function apply( \garyamort\github_io\stream\path $path, $mode)
	{
		$currentPath = $path->currentPath;
		$pos = strpos($currentPath, $this->pattern);

		if ($pos === 0)
		{
			return parent::apply($path, $mode);
		}

		return $path;


	}

} 