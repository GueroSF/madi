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
use App\Events\CommentCreatedEvent;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
    public function index(int $page, PostRepository $posts, Security $security): Response
    {
        $latestPosts = $security->isGranted('ROLE_ADMIN')
            ? $posts->findLatest($page)
            : $posts->findLatestForUser($this->getUser(), $page);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix
        return $this->render('blog/index.html.twig', [
            'paginator' => $latestPosts,
        ]);
    }

    /**
     * @Route("/posts/{slug}", methods={"GET"}, name="blog_post")
     * @IsGranted("show", subject="post", message="Posts can only be shown to attach user.")
     *
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     */
    public function postShow(Post $post): Response
    {
        // Symfony's 'dump()' function is an improved version of PHP's 'var_dump()' but
        // it's not available in the 'prod' environment to prevent leaking sensitive information.
        // It can be used both in PHP files and Twig templates, but it requires to
        // have enabled the DebugBundle. Uncomment the following line to see it in action:
        //
        // dump($post, $this->getUser(), new \DateTime());

        return $this->render('blog/post_show.html.twig', ['post' => $post]);
    }

//    /**
//     * @Route("/comment/{postSlug}/new", methods={"POST"}, name="comment_new")
//     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
//     *
//     * NOTE: The ParamConverter mapping is required because the route parameter
//     * (postSlug) doesn't match any of the Doctrine entity properties (slug).
//     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
//     */
//    public function commentNew(Request $request, Post $post, EventDispatcherInterface $eventDispatcher): Response
//    {
//        $comment = new Comment();
//        $comment->setAuthor($this->getUser());
//        $post->addComment($comment);
//
//        $form = $this->createForm(CommentType::class, $comment);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($comment);
//            $em->flush();
//
//            // When an event is dispatched, Symfony notifies it to all the listeners
//            // and subscribers registered to it. Listeners can modify the information
//            // passed in the event and they can even modify the execution flow, so
//            // there's no guarantee that the rest of this controller will be executed.
//            // See https://symfony.com/doc/current/components/event_dispatcher.html
//            $eventDispatcher->dispatch(new CommentCreatedEvent($comment));
//
//            return $this->redirectToRoute('blog_post', ['slug' => $post->getSlug()]);
//        }
//
//        return $this->render('blog/comment_form_error.html.twig', [
//            'post' => $post,
//            'form' => $form->createView(),
//        ]);
//    }

//    /**
//     * This controller is called directly via the render() function in the
//     * blog/post_show.html.twig template. That's why it's not needed to define
//     * a route name for it.
//     *
//     * The "id" of the Post is passed in and then turned into a Post object
//     * automatically by the ParamConverter.
//     */
//    public function commentForm(Post $post): Response
//    {
//        $form = $this->createForm(CommentType::class);
//
//        return $this->render('blog/_comment_form.html.twig', [
//            'post' => $post,
//            'form' => $form->createView(),
//        ]);
//    }

}
