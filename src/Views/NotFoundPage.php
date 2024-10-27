<?php

namespace App\Views;

use App\Views\BasePage;

class NotFoundPage extends BasePage
{
	public function __construct()
	{
		static::$title = implode(' ', preg_split('/(?=[A-Z])/', str_replace("App\\Views\\", "",__CLASS__)));
		parent::__construct();
	}
	public function getBody(): string
	{
		return '
			<section class="grid place-content-center text-[80px] font-bold opacity-20">
				404
			</section>';
	}
}