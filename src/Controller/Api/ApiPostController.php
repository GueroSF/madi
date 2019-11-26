<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Security\PostVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\PostInfoService;

/**
 * @Route("/blog")
 */
class ApiPostController extends AbstractController
{
    /**
     * @var PostInfoService
     */
    private $postService;

    public function __construct(PostInfoService $postService)
    {
        $this->postService = $postService;
    }

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

        $this->postService->findPostInfo($this->getUser(), $post);
        $this->postService->markAsRead();

        return new JsonResponse([
            'title'   => $post->getTitle(),
            'content' => $post->getContent(),
            'isSign'  => $this->postService->isSign()
        ]);
    }

    /**
     * @Route("/{id}/sign", methods={"POST"}, name="api_blog_post_sign")
     * @IsGranted("sign", subject="post")
     */
    public function sign(Request $request, Post $post): Response
    {
        if (!$this->isCsrfTokenValid('sign', $request->request->get('token'))) {
            return $this->json('false', 403);
        }

        $this->postService->findPostInfo($this->getUser(), $post);
        $this->postService->markAsSigh();

        $this->addFlash('success', 'post.sign_successfully');

        return $this->json('true');
    }
}
