<?php declare(strict_types=1);

namespace BearSys\WSF\Configuration\Types;

use Symfony\Component\Yaml\Yaml as SfYaml;

class Yaml implements IType
{
	public static function getExtensionList(): array
	{
		return [
			'yaml',
			'yml',
		];
	}


	public static function parse(string $content): array
	{
		return SfYaml::parse($content, SfYaml::PARSE_EXCEPTION_ON_INVALID_TYPE + SfYaml::PARSE_CONSTANT);
	}
}