<?php

declare(strict_types=1);

namespace RunApi\Imagen4;

/**
 * Constants for model slugs supported by the Imagen 4 PHP SDK.
 */
final class Types
{
    /** @var list<string> */
    public const TEXT_TO_IMAGE_MODELS = ['imagen-4', 'imagen-4-fast', 'imagen-4-ultra'];

    /** @var list<string> */
    public const REMIX_IMAGE_MODELS = ['imagen-4-pro-remix-image'];

    private function __construct()
    {
    }
}
