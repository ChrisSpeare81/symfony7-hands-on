<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentsType;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post_index')]
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAllWithComments(),
        ]);
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    public function show(MicroPost $post): Response {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2)]
    public function add(Request $request, EntityManagerInterface $entityManager): Response{

        $form = $this->createForm(MicroPostType::class, new MicroPost());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new DateTime());

            $entityManager->persist($post);
            $entityManager->flush();

            // Add a flash
            $this->addFlash('success', 'Your micro post has been added');

            return $this->redirectToRoute('app_micro_post_index');
        }

        return $this->render('micro_post/add.html.twig', ['form' => $form]);
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    public function edit(MicroPost $post, Request $request, EntityManagerInterface $entityManager): Response{

        $form = $this->createForm(MicroPostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $entityManager->persist($post);
            $entityManager->flush();

            // Add a flash
            $this->addFlash('success', 'Your micro post has been updated');

            return $this->redirectToRoute('app_micro_post_index');
        }

        return $this->render('micro_post/edit.html.twig', ['form' => $form]);
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    public function addComment(MicroPost $post, Request $request, EntityManagerInterface $entityManager): Response{

        $form = $this->createForm(CommentsType::class, new Comment());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment = $form->getData();
            $comment->setPost($post);

            $entityManager->persist($comment);
            $entityManager->flush();

            // Add a flash
            $this->addFlash('success', 'Your comment has been updated');

            return $this->redirectToRoute('app_micro_post_show', [ 'post' => $post->getId() ]);

        }

        return $this->render('micro_post/comment.html.twig', ['form' => $form, 'post' => $post]);
    }
}
