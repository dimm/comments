<?php
namespace dimm\comments\Interfaces;

/**
 * @author dimm <dimm.mds@gmail.com>
 */
interface IBranch{
	
	/**
	 * Determines the branch has childs
	 * @return boolean
	 */
	public function hasChilds();

	/**
	 * Returns the branch childs
	 * @return IBranch
	 */
	public function getChilds();
}