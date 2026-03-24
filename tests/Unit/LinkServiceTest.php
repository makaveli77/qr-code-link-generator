<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LinkService;
use App\Repositories\LinkRepository;
use App\DTOs\CreateLinkDTO;
use App\Models\Link;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Support\Str;

class LinkServiceTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected LinkService $linkService;
    protected MockInterface $linkRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->linkRepository = Mockery::mock(LinkRepository::class);
        $this->linkService = new LinkService($this->linkRepository);
    }

    public function test_it_generates_unique_short_code_when_alias_is_not_provided()
    {
        $dto = new CreateLinkDTO(
            originalUrl: 'https://example.com',
            userId: 1,
            customAlias: null
        );

        $this->linkRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($linkData) {
                $this->assertNotNull($linkData['short_code']);
                $this->assertEquals(6, strlen($linkData['short_code']));
                return true;
            }), Mockery::any())
            ->andReturn(new Link());

        $this->linkService->createLink($dto);
    }

    public function test_it_uses_custom_alias_if_provided()
    {
        $dto = new CreateLinkDTO(
            originalUrl: 'https://example.com',
            userId: 1,
            customAlias: 'my-custom-alias'
        );

        $this->linkRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($linkData) {
                $this->assertEquals('my-custom-alias', $linkData['short_code']);
                return true;
            }), Mockery::any())
            ->andReturn(new Link());

        $this->linkService->createLink($dto);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
