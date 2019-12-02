<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Security\PostVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\PostInfoService;

/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @Route("/blog")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class BlogController extends AbstractController
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
     * @Route("/", defaults={"page": "1"}, methods={"GET"}, name="blog_index")
     * @Route("/page/{page<[1-9]\d*>}", methods={"GET"}, name="blog_index_paginated")
     * @Cache(smaxage="10")
     *
     * NOTE: For standard formats, Symfony will also automatically choose the best
     * Content-Type header for the response.
     * See https://symfony.com/doc/current/quick_tour/the_controller.html#using-formats
     */
    public function index(int $page, PostRepository $posts): Response
    {
        $latestPosts = $posts->findLatestForUser($this->getUser(), $page);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix
        return $this->render('blog/index.html.twig', [
            'paginator' => $latestPosts,
        ]);
    }

    /**
     * @Route("/posts/{id}", methods={"GET"}, name="blog_post")
     */
    public function postShow(Post $post): Response
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        if (!$isAdmin && !$this->isGranted(PostVoter::SHOW, $post)) {
            throw $this->createAccessDeniedException('Posts can only be shown to attach user.');
        }

        $this->postService->findPostInfo($this->getUser(), $post);

        if (!$isAdmin) {
            $this->postService->markAsRead();
        }

        return $this->render('blog/post_show.html.twig', [
            'post'   => $post,
            'isSign' => $this->postService->isSign()
        ]);
    }

    /**
     * @Route("/{id}/sign", methods={"POST"}, name="blog_post_sign")
     * @IsGranted("sign", subject="post")
     */
    public function sign(Request $request, Post $post): Response
    {
        if (!$this->isCsrfTokenValid('sign', $request->request->get('token'))) {
            return $this->redirectToRoute('blog_index');
        }

        $this->postService->findPostInfo($this->getUser(), $post);
        $this->postService->markAsSigh();

        $this->addFlash('success', 'post.sign_successfully');

        return $this->redirectToRoute('blog_index');
    }

}
