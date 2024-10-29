<?php

namespace App\Enums;

enum ApiPathEnum: string
{
	case CREATE = '/api/v1/users/create';
	case GET = '/api/v1/users/get';
	case  UPDATE = '/api/v1/users/update';
	case DELETE = '/api/v1/users/delete';
}
