<?php

namespace App\Services;

use App\Controllers\ApiController;
use App\Controllers\PageController;
use App\Enums\ApiPathEnum;
use App\Enums\PathEnums;

class Router
{
	protected string $url;
	protected string $method;

	public function __construct()
	{
		$this->url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$this->method = $_SERVER['REQUEST_METHOD'];
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function returnResponse(): void
	{
		match ($this->url) {
			PathEnums::HOME->value      => PageController::index(),
			PathEnums::USER->value      => PageController::show(),
			ApiPathEnum::GET->value     => ApiController::get(),
			ApiPathEnum::CREATE->value  => ApiController::create(),
			ApiPathEnum::UPDATE->value  => ApiController::update(),
			ApiPathEnum::DELETE->value  => ApiController::delete(),
			default                     => PageController::notFound()
		};
	}
}