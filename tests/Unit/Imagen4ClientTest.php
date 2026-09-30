<?php

declare(strict_types=1);

namespace RunApi\Imagen4\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\Imagen4\Imagen4Client;
use RunApi\Imagen4\Models\CompletedImageTaskResponse;
use RunApi\Imagen4\Resources\RemixImage;
use RunApi\Imagen4\Resources\TextToImage;

final class Imagen4ClientTest extends TestCase
{
    public function testExposesTypedResources(): void
    {
        $client = new Imagen4Client(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        self::assertInstanceOf(TextToImage::class, $client->textToImage);
        self::assertInstanceOf(RemixImage::class, $client->remixImage);
    }

    public function testCreatePostsCompactedBodyToCorrectPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}')]);
        $client = new Imagen4Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $task = $client->textToImage->create([
            'model' => 'imagen-4',
            'prompt' => 'A product render',
            'callback_url' => '',
            'seed' => null]);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('task_1', $task->id);
        self::assertSame('/api/v1/imagen_4/text_to_image', $transport->requests[0]->getUri()->getPath());
        self::assertSame('imagen-4', $body['model']);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('seed', $body);
    }

    public function testRunReturnsTypedCompletedResponseAndPreservesUnknownFields(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","images":[{"url":"https://file.runapi.ai/result"}],"extra_field":"kept","usage":{"cost":0.05}}')]);
        $client = new Imagen4Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->textToImage->run([
            'model' => 'imagen-4',
            'prompt' => 'A product render']);

        self::assertInstanceOf(CompletedImageTaskResponse::class, $result);
        self::assertSame('https://file.runapi.ai/result', $result->images[0]->url);
        self::assertSame('kept', $result->toArray()['extra_field']);
        self::assertSame('/api/v1/imagen_4/text_to_image/task_1', $transport->requests[1]->getUri()->getPath());
    }

    public function testCompletedResponseRequiresResultFiles(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","usage":{"cost":0.05}}')]);
        $client = new Imagen4Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('images is required');

        $client->textToImage->run([
            'model' => 'imagen-4',
            'prompt' => 'A product render']);
    }



    public function testSecondaryResourceUsesItsOwnPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_2"}')]);
        $client = new Imagen4Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->remixImage->create([
            'model' => 'imagen-4-pro-remix-image',
            'prompt' => 'A product render',
            'source_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg']]);

        self::assertSame('/api/v1/imagen_4/remix_image', $transport->requests[0]->getUri()->getPath());
    }
}
