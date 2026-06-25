<?php

declare(strict_types=1);

namespace RunApi\Imagen4\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\Imagen4\Models\CompletedImageTaskResponse;
use RunApi\Imagen4\Models\ImageTaskResponse;
use RunApi\Imagen4\Types;

/**
 * Generates new images guided by one or more source images combined with a text prompt. Accepts up to 8 source images. Supports output resolution control (1k/2k/4k) and format selection (png/jpg).
 */
readonly class RemixImage extends TypedConfiguredResource
{
    /**
     * Submits a remix-image task and returns immediately with a task id.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   source_image_urls: list<string>,
     *   aspect_ratio?: string,
     *   callback_url?: string,
     *   output_format?: string,
     *   output_resolution?: string
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Fetches the current status of a remix-image task by id.
     */
    public function get(string $id, ?RequestOptions $options = null): ImageTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var ImageTaskResponse $response */
        return $response;
    }

    /**
     * Submits a remix-image task and polls until it completes.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   source_image_urls: list<string>,
     *   aspect_ratio?: string,
     *   callback_url?: string,
     *   output_format?: string,
     *   output_resolution?: string
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedImageTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedImageTaskResponse $response */
        return $response;
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            '/api/v1/imagen_4/remix_image',
            'imagen-4/remix-image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
            Types::REMIX_IMAGE_MODELS,
            'remix-image',
            ImageTaskResponse::class,
            CompletedImageTaskResponse::class,
        );
    }
}
