<?php declare(strict_types=1);

namespace BearSys\WSF\Handlers;

interface IHandler
{
	function onMessage(string $message): void;
}