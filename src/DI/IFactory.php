<?php declare(strict_types=1);

namespace BearSys\WSF\DI;

interface IFactory
{
	function create(\stdClass $configuration);
}