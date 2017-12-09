<?php

namespace dimm\comments\Base;

use dimm\comments\Interfaces\IEntity;

class BEntity implements IEntity{	

	protected $data = [];

	public $childs;

	protected $attributes = [
		'id',
		'parent_id',
		'key',
		'comment',
		'created_at',
		'updated_at',
		'user_id',
		'username',
		'status'

	];


	public function ID(){
		$this->id;
	}

	public function ParentID(){
		$this->parent_id;
	}

	public function Key(){
		$this->key;
	}


	public function __construct(){
		$this->updated_at = time();
		$this->created_at = time();
	}

	public function getAttributes(){
		return $this->attributes;
	}

	public function __set($attr, $val){
		if(in_array($attr, $this->attributes))
			$this->data[$attr] = $val;
	}

	public function __get($attr){
		if(in_array($attr, $this->attributes))
			return isset($this->data[$attr]) ? $this->data[$attr] : null;
	}


}
