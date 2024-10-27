<?php

namespace App\Views;

use App\Controllers\ApiController;
use App\Controllers\PageController;
use App\Enums\ActionsEnum;
use App\Enums\PathEnums;

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
		$user = $this->data['result'] ?? null;
		$full_name = $user['full_name'] ?? null;
		$role = $user['role'] ?? null;
		$efficiency = $user['efficiency'] ?? null;
		$action = isset($user) ? ActionsEnum::UPDATE->value : ActionsEnum::CREATE->value;

		$success = '
		    <form action="" method="POST">
		        <div class="sm:col-span-3">
		            <label for="full_name"
		                   class="block text-sm font-medium leading-6 text-gray-900">Full name</label>
		            <div class="mt-2">
		                <input type="text" name="full_name" id="full_name" autocomplete="full_name"
		                       value=" ' . $full_name . '" required
		                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
		            </div>
		        </div>
		        <div class="sm:col-span-3">
		            <label for="role"
		                   class="block text-sm font-medium leading-6 text-gray-900">Role</label>
		            <div class="mt-2">
		                <input type="text" name="role" id="role" autocomplete="role"
		                       value="' . $role . '" required
		                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
		            </div>
		        </div>
		        <div class="sm:col-span-3">
		            <label for="efficiency"
		                   class="block text-sm font-medium leading-6 text-gray-900">Efficiency</label>
		            <div class="mt-2">
		                <input type="number" name="efficiency" id="efficiency" autocomplete="efficiency" min="0" max="100"
		                       value="' . $efficiency . '" required
		                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
		            </div>
		        </div>
		        <section class="mt-6 flex item-center justify-between">
		            <button type="submit"
		                    name="action"
		                    value="' . $action . '"
		                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
		                Save user
		            </button>
		            <button type="submit"
		                    name="action"
		                    value="delete"
		                    class="block rounded-md bg-red-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
		                Delete user
		            </button>
		        </section>
		    </form>
		
		';

		$error = '
		    <section class="grid place-content-center text-[80px] font-bold opacity-20">
		        User not found
		    </section>
		';

		return $this->data['success'] ? $success : $error;
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