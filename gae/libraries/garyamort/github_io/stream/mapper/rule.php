<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 12:52 PM
 */

namespace garyamort\github_io\stream\mapper;


abstract class rule {

	public $pattern;
	public $replacement;

	/*
 * What mode limits to use for this rule
 */
	public $modeLimits = false;

	/*
	 * What stream context to apply to rewritten paths
	 */
	public $contextOptions = false;


	public function apply( \garyamort\github_io\stream\path $path, $mode)
	{

		// If the path has been changed by this rule
		if ($path->pathChangedRecently())
		{
			// If this rule has mode limitations, copy them to the path
			if ($this->modeLimits)
			{
				$path->modeLimits = array_merge($path->modeLimits, $this->modeLimits);
			}

			// If this rule has context options, add them for the path
			if ($this->contextOptions)
			{
				$path->contextOptions = array_merge($path->contextOptions, $this->contextOptions);
			}

			// Reset the path as unchanged
			$path->truePath = $path->currentPath;


		}


		return $path;
	}

} 