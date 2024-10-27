<?php

namespace App\Contracts;

interface CrudInterface
{
	public static function create();
	public static function get();
	public static function update();
	public static function delete();
}