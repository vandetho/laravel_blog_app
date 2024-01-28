<?php

use App\Models\Blog;
use App\Workflow\State\BlogState;
use App\Workflow\Transition\BlogTransition;

return [
    'blog_publishing' => [
        'type' => 'state_machine',
        'audit_trail' => ['enabled' => true],
        'marking_store' => [
            'type' => 'method',
            'property' => 'state'
        ],
        'supports' => [Blog::class],
        'places' => [
            BlogState::NEW_BLOG,
            BlogState::NEED_REVIEW,
            BlogState::NEED_UPDATE,
            BlogState::PUBLISHED,
        ],
        'initial_marking' => BlogState::NEW_BLOG,
        'transitions' => [
            BlogTransition::CREATE_BLOG => [
                'from' => BlogState::NEW_BLOG,
                'to' => BlogState::NEED_REVIEW,
            ],
            BlogTransition::PUBLISH => [
                'from' => BlogState::NEED_REVIEW,
                'to' => BlogState::PUBLISHED,
            ],
            BlogTransition::NEED_REVIEW => [
                'from' => BlogState::PUBLISHED,
                'to' => BlogState::NEED_REVIEW,
            ],
            BlogTransition::REJECT => [
                'from' => BlogState::NEED_REVIEW,
                'to' => BlogState::NEED_UPDATE,
            ],
            BlogTransition::UPDATE => [
                'from' => BlogState::NEED_UPDATE,
                'to' => BlogState::NEED_REVIEW,
            ],
        ],
    ],
];
