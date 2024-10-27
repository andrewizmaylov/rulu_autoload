<?php

namespace App\Contracts;

interface PageInterface
{
	public function getHeader(): string;
	public function getBody(): string;
	public function getFooter(): string;
}