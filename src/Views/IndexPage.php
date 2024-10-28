<?php

namespace App\Views;

use App\Services\Builder;

class IndexPage extends BasePage
{
	public function __construct()
	{
		static::$title = implode(' ', preg_split('/(?=[A-Z])/', str_replace("App\\Views\\", "",__CLASS__)));
		parent::__construct();
	}
	public function getBody(): string
	{
		return Builder::renderTable($this->data);
	}
}