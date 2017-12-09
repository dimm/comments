<?php
namespace dimm\comments\Interfaces;

interface IRepository{

	public function get($id);

	public function getAll(array $search);

	public function delete($id);
	
	public function create(array $data);

	public function update(array $data);
}