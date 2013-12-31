<?php
/**
 * Created by PhpStorm.
 * User: gmort
 * Date: 12/30/13
 * Time: 3:04 PM
 */

namespace garyamort\github_io\stream;


class path {

	/*
	 * What mode limits to use for this path
	 */
	public $modeLimits = false;

	/*
	 * What stream context options to use to open this path
	 */
	public $contextOptions;

	/*
	 * What the original path was
	 */
	public $originalPath;

	/*
 * What the current path is
 */
	public $currentPath;

	/*
	 * What the true path is
	 */
	public $truePath;


	public function __construct($pathname)
	{
		$this->originalPath = $pathname;
		$this->currentPath = $pathname;
		$this->truePath = $pathname;
	}

	public function pathChanged()
	{
		if ($this->truePath === $this->originalPath)
		{
			return false;
		}
		return true;
	}

	public function pathChangedRecently()
	{
		if ($this->truePath === $this->currentPath)
		{
			return false;
		}
		return true;
	}
} 