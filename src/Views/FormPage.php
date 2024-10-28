<?php

namespace App\Views;

use App\Controllers\ApiController;
use App\Controllers\PageController;
use App\Enums\ActionsEnum;
use App\Enums\PathEnums;
use App\Services\Builder;

class FormPage extends BasePage
{
	public function __construct()
	{
		if ($_SERVER["REQUEST_METHOD"] === "POST") {
			$this->updateData();
		}
		static::$title = implode(' ', preg_split('/(?=[A-Z])/', str_replace("App\\Views\\", "",__CLASS__)));
		parent::__construct();
	}

	public function getBody(): string
	{
		if (!$this->data['success']) return Builder::renderError();

		return !empty($this->data['result']) && count($this->data['result']) > 1
			? Builder::renderTable($this->data) : Builder::renderForm($this->data['result'][0] ?? null);
	}

	private function updateData(): void
	{
		$action = $_POST['action'];

		match ($action) {
			ActionsEnum::CREATE->value => $result = ApiController::create(),
			ActionsEnum::UPDATE->value => $result = ApiController::update(),
			ActionsEnum::DELETE->value => $result = ApiController::delete(),
		};

		$success = json_decode($result, true)['success'];
		if ($success) {
			header('Location: ' . PathEnums::HOME->value, true, 301);
		}
	}
}