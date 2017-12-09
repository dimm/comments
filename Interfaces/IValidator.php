<?php
namespace dimm\comments\Interfaces;

interface IValidator{

	public function validate(array $data);

	public function getErrors();
}