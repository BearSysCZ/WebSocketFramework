<?php declare(strict_types=1);

namespace BearSys\WSF\Configuration\Types;

interface IType
{
	static function getExtensionList(): array;
	static function parse(string $content): array;
}