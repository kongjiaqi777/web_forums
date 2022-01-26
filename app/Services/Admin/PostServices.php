<?php

namespace App\Services\Admin;

use App\Services\BaseServices;
use App\Repositories\PostRepository;

class PostServices extends BaseServices
{
    private $postRepos;

    public function __construct(PostRepository $postRepos)
    {
        $this->postRepos = $postRepos;
    }

    public function list()
    {

    }
}
