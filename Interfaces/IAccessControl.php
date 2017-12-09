<?php
namespace dimm\comments\Interfaces;

interface IAccessControl{

	public function can($action, IEntity $entity);
}