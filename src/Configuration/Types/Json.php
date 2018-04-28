<?php declare(strict_types=1);

namespace BearSys\WSF\Configuration\Types;

class Json implements IType
{
	public static function getExtensionList(): array
	{
		return [
			'json',
		];
	}

	public static function parse(string $content): array
	{
		return json_decode($content, TRUE);
	}
}