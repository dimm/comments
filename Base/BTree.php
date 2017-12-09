<?php
namespace dimm\comments\Base;

use dimm\comments\Interfaces\ITree;
use dimm\comments\Base\BBranch;

class BTree implements ITree{

	protected $chain;

	protected $tree;

	public function __construct(array $entities_chain){
		$this->chain = $entities_chain;
	}
	
	public function get(){
		if (null === $this->tree)
			$this->tree = $this->buildTree();
		return $this->tree;
	}

	protected function buildTree(){
		$items = [];
		foreach ($this->chain as $entity){
			$branch = new BBranch;
			$branch->entity = $entity;
			$items[] = $branch;
		}
	
		$childs = array();
		foreach ($items as &$item) 
			$childs[$item->entity->parent_id][] = &$item;
		unset($item);

		foreach ($items as &$item)
			if (isset($childs[$item->entity->id]))
				$item->childs = $childs[$item->entity->id];
		if (isset($childs[0])) 
			return $childs[0];
		return [];
	}
}