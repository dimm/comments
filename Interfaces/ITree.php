<?php
namespace dimm\comments\Interfaces;

interface ITree{

	public function __construct(array $entities_chain);
	
	public function get();

}