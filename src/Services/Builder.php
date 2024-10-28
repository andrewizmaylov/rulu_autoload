<?php

namespace App\Services;

use App\Enums\ActionsEnum;

class Builder
{
	public static function renderTable($data): string
	{
		if (!$data['success']) {
			return '
				<section class="grid place-content-center text-[80px] font-bold opacity-20">
					No data
				</section>';
		} else {
			$body = '
			<div class="px-4 sm:px-6 lg:px-8">
			    <div class="sm:flex sm:items-center">
			        <div class="sm:flex-auto">
			            <h1 class="text-base font-semibold leading-6 text-gray-900">Users</h1>
			            <p class="mt-2 text-sm text-gray-700">A list of all the users in your account including their name, title,
			                email and role.</p>
			        </div>
			        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
			            <a href="/user"
			               class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
			                Add User
			            </a>
			        </div>
			    </div>
			    <div class="mt-8 flow-root">
			        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
			            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
			                <table class="min-w-full divide-y divide-gray-300">
			                    <thead>
			                    <tr>
			                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
			                            Name
			                        </th>
			                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
			                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">efficiency
			                        </th>
			                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
			                            <span class="sr-only">Edit</span>
			                        </th>
			                    </tr>
			                    </thead>
			                    <tbody class="divide-y divide-gray-200">';


			foreach ($data['result'] as $user) {
				$body .= self::getTableRow($user);
			}

			$body .= '
			                    </tbody>
			                </table>
			            </div>
			        </div>
			    </div>
			</div>
			';

			return $body;
		}
	}

	private static function getTableRow($user): string
	{
		return '
            <tr>
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                    '.$user['full_name'].'
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    '.$user['role'].'
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    '.$user['efficiency'].'
                </td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                    <a href="/user?id='.$user['id'].'"
                       class="text-indigo-600 hover:text-indigo-900">
                        Edit
                    </a>
                </td>
            </tr>';
	}

	public static function renderForm($user): string
	{
		$full_name = $user['full_name'] ?? null;
		$role = $user['role'] ?? null;
		$efficiency = $user['efficiency'] ?? null;
		$action = isset($user) ? ActionsEnum::UPDATE->value : ActionsEnum::CREATE->value;

		return '
			<form action="" method="POST">
		        <div class="sm:col-span-3">
		            <label for="full_name"
		                   class="block text-sm font-medium leading-6 text-gray-900">Full name</label>
		            <div class="mt-2">
		                <input type="text" name="full_name" id="full_name" autocomplete="full_name"
		                       value=" '.$full_name.'" required
		                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
		            </div>
		        </div>
		        <div class="sm:col-span-3">
		            <label for="role"
		                   class="block text-sm font-medium leading-6 text-gray-900">Role</label>
		            <div class="mt-2">
		                <input type="text" name="role" id="role" autocomplete="role"
		                       value="'.$role.'" required
		                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
		            </div>
		        </div>
		        <div class="sm:col-span-3">
		            <label for="efficiency"
		                   class="block text-sm font-medium leading-6 text-gray-900">Efficiency</label>
		            <div class="mt-2">
		                <input type="number" name="efficiency" id="efficiency" autocomplete="efficiency" min="0" max="100"
		                       value="'.$efficiency.'" required
		                       class="px-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
		            </div>
		        </div>
		        <section class="mt-6 flex item-center justify-between">
		            <button type="submit"
		                    name="action"
		                    value="'.$action.'"
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
	}

	public static function renderError(): string
	{
		return '
			<section class="grid place-content-center text-[80px] font-bold opacity-20">
		        User not found
		    </section>
		';
	}
}