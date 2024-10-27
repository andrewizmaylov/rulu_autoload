<?php

namespace App\Enums;

enum ActionsEnum: string
{
	case CREATE = 'create';
	case UPDATE = 'update';
	case DELETE = 'delete';
}
