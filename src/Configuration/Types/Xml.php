<?php declare(strict_types=1);

namespace BearSys\WSF\Configuration\Types;

class Xml implements IType
{
	public static function getExtensionList(): array
	{
		return [
			'xml',
		];
	}


	public static function parse(string $content): array
	{
		$xml = simplexml_load_string($content);
		return json_decode(json_encode($xml), TRUE);
	}
}