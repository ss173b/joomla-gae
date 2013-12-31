<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:52 PM
 */

namespace garyamort\github_io\stream\mapper;


class regex extends rule {

	public $limit = 1;

	public function apply( $path, $mode)
	{


		$currentPath = $path->currentPath;


		// Perform any path replacement
		$newPath = preg_replace($this->pattern,
			$this->replacement,
			$currentPath,
			$this->limit
							);

		// Reset the current path to the potential new path
		$path->currentPath = $newPath;


		return parent::applyRule($path, $mode);

	}

} 