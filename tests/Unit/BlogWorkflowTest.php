<?php

namespace Tests\Unit;

use App\Workflow\BlogWorkflow;
use App\Workflow\State\BlogState;
use Faker\Factory;
use Tests\TestCase;

/**
 * Class BlogWorkflowTest
 * @package Tests\Unit
 * @author  Vandeth THO <thovandeth@gmail.com>
 */
class BlogWorkflowTest extends TestCase
{
    public function testCreateBlog(): int
    {
        $faker = Factory::create();
        $title = $faker->sentence();
        $content = $faker->paragraph();
        $blog = app(BlogWorkflow::class)->create($title, $content);

        $this->assertNotNull($blog->id);
        $this->assertSame($title, $blog->title);
        $this->assertSame($content, $blog->content);
        $this->assertSame(BlogState::NEED_REVIEW, $blog->state);
        return $blog->id;
    }

    /**
     * @depends testCreateBlog
     */
    public function testReject(int $blogId): int
    {
        $blog = app(BlogWorkflow::class)->reject($blogId);
        $this->assertSame(BlogState::NEED_UPDATE, $blog->state);
        return $blog->id;
    }

    /**
     * @depends testCreateBlog
     */
    public function testUpdate(int $blogId): int
    {
        $faker = Factory::create();
        $title = $faker->sentence();
        $content = $faker->paragraph();

        $blog = app(BlogWorkflow::class)->update($blogId, $title, $content);
        $this->assertSame($title, $blog->title);
        $this->assertSame($content, $blog->content);
        $this->assertSame(BlogState::NEED_REVIEW, $blog->state);
        return $blog->id;
    }

    /**
     * @depends testCreateBlog
     */
    public function testPublish(int $blogId): int
    {
        $blog = app(BlogWorkflow::class)->publish($blogId);
        $this->assertSame(BlogState::PUBLISHED, $blog->state);
        return $blog->id;
    }

    /**
     * @depends testCreateBlog
     */
    public function testNeedReview(int $blogId): int
    {
        $blog = app(BlogWorkflow::class)->needReview($blogId);
        $this->assertSame(BlogState::NEED_REVIEW, $blog->state);
        return $blog->id;
    }
}
