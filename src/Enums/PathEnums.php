<?php

namespace App\Enums;

enum PathEnums: string
{
	case HOME = '/';
	case USER = '/user';
	case API = '/api/v1/users';
}
