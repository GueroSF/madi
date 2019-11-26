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
use App\Entity\PostInfo;
use App\Repository\PostInfoRepository;
use App\Repository\PostRepository;
use App\Security\PostVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/posts/{slug}", methods={"GET"}, name="blog_post")
     */
    public function postShow(Post $post): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted(PostVoter::SHOW, $post)) {
            throw $this->createAccessDeniedException('Posts can only be shown to attach user.');
        }

        /** @var PostInfoRepository $repo */
        $repo = $this->getDoctrine()->getRepository(PostInfo::class);

        $info = $repo->findByUserAndPost($this->getUser(), $post);
        if ($info->getReaderAt() === null) {
            $info->setReaderAt(new \DateTime());
        }

        $this->getDoctrine()->getManager()->persist($info);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('blog/post_show.html.twig', ['post' => $post]);
    }

}
