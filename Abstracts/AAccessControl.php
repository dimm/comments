<?php
namespace dimm\comments\Abstracts;

use dimm\comments\Interfaces\IAccessControl;
use dimm\comments\Interfaces\IEntity;

abstract class AAccessControl implements IAccessControl{

	const CAN_VIEW = 'can_view';
	const CAN_DELETE = 'can_delete';
	const CAN_UPDATE = 'can_update';
	const CAN_PUT = 'can_put';
	const CAN_ANSWER = 'can_answer';


	public abstract function can($action, IEntity $entity);
}