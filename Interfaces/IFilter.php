<?php
namespace dimm\comments\Interfaces;

/**
 * @author dimm.mds <dimm.mds@gmail.com>
 */
interface IFilter{
	/**
	 * Clears string from forbidden words
	 * @param  array $data input raw data
	 */
	public function getFiltred(array &$data);

}