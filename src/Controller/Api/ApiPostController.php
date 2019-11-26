<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Security\PostVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\ShowPost;

/**
 * @Route("/blog")
 */
class ApiPostController extends AbstractController
{
    /**
     * @Route("/",defaults={"page": "1"}, methods={"GET"}, name="api_blog_index")
     * @Route("/page/{page<[1-9]\d*>}", methods={"GET"}, name="api_blog_index_paginated")
     * @return Response
     */
    public function index(int $page, PostRepository $posts): Response
    {
        $latestPosts = $posts->findLatestForUser($this->getUser(), $page);

        return new JsonResponse($latestPosts->getResults());
    }

    /**
     * @Route("/posts/{slug}", methods={"GET"}, name="api_blog_post")
     */
    public function postShow(Post $post): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted(PostVoter::SHOW, $post)) {
            throw $this->createAccessDeniedException('Posts can only be shown to attach user.');
        }

        $this->get(ShowPost::class)->markAsRead($this->getUser(), $post);

        return new JsonResponse([
            'title'   => $post->getTitle(),
            'content' => $post->getContent(),
        ]);
    }
}
