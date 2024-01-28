<?php
declare(strict_types=1);

namespace App\Workflow;


use App\Models\Blog;
use App\Workflow\State\BlogState;
use App\Workflow\Transition\BlogTransition;
use Illuminate\Support\Facades\DB;
use LogicException;
use Symfony\Component\Workflow\Registry;

/**
 * Class BlogWorkflow
 *
 * @package App\Workflow
 * @author Vandeth THO <thovandeth@gmail.com>
 */
readonly class BlogWorkflow
{
    /**
     * BlogWorkflow constructor.
     *
     * @param Registry $workflowRegistry
     */
    public function __construct(private Registry $workflowRegistry)
    {}

    /**
     * Update the blog and send it to be reviewed
     *
     * @param string $title
     * @param string $content
     * @return Blog
     */
    public function create(string $title, string $content): Blog
    {
        $blog = new Blog();
        $blog->title = $title;
        $blog->content = $content;

        $blogStateMachine = $this->workflowRegistry->get($blog, 'blog_publishing');
        $blogStateMachine->apply($blog, BlogTransition::CREATE_BLOG);
        $blog->save();
        return $blog;
    }

    /**
     * Reject the blog and send it to be updated
     *
     * @param int $blogId
     * @return Blog
     */
    public function needReview(int $blogId): Blog
    {
        $blog = $this->getBlog($blogId);
        $blogStateMachine = $this->workflowRegistry->get($blog, 'blog_publishing');

        $blogStateMachine->apply($blog, BlogTransition::NEED_REVIEW);
        $blog->save();
        return $blog;
    }

    /**
     * Reject the blog and send it to be updated
     *
     * @param int $blogId
     * @return Blog
     */
    public function reject(int $blogId): Blog
    {
        $blog = $this->getBlog($blogId);
        $blogStateMachine = $this->workflowRegistry->get($blog, 'blog_publishing');

        $blogStateMachine->apply($blog, BlogTransition::REJECT);
        $blog->save();
        return $blog;

    }

    /**
     * Update the blog and send it to be reviewed
     *
     * @param int $blogId
     * @param string|null $title
     * @param string|null $content
     * @return Blog
     */
    public function update(int $blogId, ?string $title, ?string $content): Blog
    {
        $blog = $this->getBlog($blogId);
        if ($title) {
            $blog->title = $title;
        }
        if ($content) {
            $blog->content = $content;
        }
        $blogStateMachine = $this->workflowRegistry->get($blog, 'blog_publishing');

        $blogStateMachine->apply($blog, BlogTransition::UPDATE);
        $blog->save();
        return $blog;
    }

    /**
     * Approve the blog and publish it
     *
     * @param int $blogId
     * @return Blog
     */
    public function publish(int $blogId): Blog
    {
        $blog = $this->getBlog($blogId);
        $blogStateMachine = $this->workflowRegistry->get($blog, 'blog_publishing');

        $blogStateMachine->apply($blog, BlogTransition::PUBLISH);
        $blog->save();
        return $blog;
    }

    private function getBlog(int $blogId): Blog
    {
        $blog = Blog::find($blogId);

        if ($blog) {
            return $blog;
        }

        throw new LogicException('Blog not found');
    }
}
