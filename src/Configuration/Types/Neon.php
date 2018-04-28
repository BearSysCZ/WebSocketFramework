<?php declare(strict_types=1);

namespace BearSys\WSF\Configuration\Types;

class Neon implements IType
{
	public static function getExtensionList(): array
	{
		return [
			'neon',
		];
	}


	public static function parse(string $content): array
	{
		return \Nette\Neon\Neon::decode($content);
	}
}