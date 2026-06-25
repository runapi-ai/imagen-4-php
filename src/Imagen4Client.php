<?php

declare(strict_types=1);

namespace RunApi\Imagen4;

use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\Imagen4\Resources\RemixImage;
use RunApi\Imagen4\Resources\TextToImage;

/**
 * Provides text-to-image generation and remix (image-guided generation) for Imagen 4.
 *
 * Exposes typed model resources plus the universal files and account resources.
 */
final class Imagen4Client extends BaseClient
{
    /**
     * Text to image operations.
     */
    public readonly TextToImage $textToImage;
    /**
     * Remix image operations.
     */
    public readonly RemixImage $remixImage;

    /**
     * Create an Imagen 4 client with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        parent::__construct($options);
        $this->textToImage = TextToImage::fromHttp($this->http);
        $this->remixImage = RemixImage::fromHttp($this->http);
    }
}
