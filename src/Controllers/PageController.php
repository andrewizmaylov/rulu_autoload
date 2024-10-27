<?php

namespace App\Controllers;

use App\Views\FormPage;
use App\Views\IndexPage;
use App\Views\NotFoundPage;

class PageController {

	public static function index(): IndexPage
	{
		return new IndexPage();
	}

	public static function show(): FormPage
	{
		return new FormPage();
	}

	public static function notFound(): NotFoundPage
	{
		return new NotFoundPage();
	}

}