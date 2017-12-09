<?php
namespace dimm\comments\Base;
use dimm\comments\Interfaces\IBranch;

class BBranch implements IBranch{
	
	public $entity;

	public $childs;

	public function hasChilds(){
		return $this->childs === null ? false : true;
	}

	public function getChilds(){
		return $this->childs;
	}
}