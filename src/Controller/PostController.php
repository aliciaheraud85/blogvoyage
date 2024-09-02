<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Rubrik;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\RubrikRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    private $reppo;

    public function __construct(PostRepository $reppo){
         $this-> reppo = $reppo;
    }

    #[Route('/', name: 'app_post')]
    public function index(): Response
    {
        $postCarrousel = $this->reppo->findBy([], ['createdAt' => 'DESC'], 4, 4);
        $posts = $this->reppo->findBy([], ['createdAt' => 'DESC'],3, 12);
        return $this->render('post/index.html.twig', [
            'postCarrousel' => $postCarrousel,
            'posts' => $posts
        ]);
    }

    #[Route('/rubrik/rubrik/{id}', name:'posts_by_rubrik')]
    public function postByRubrik(Rubrik $rubrik, $id, PostRepository $prepo, RubrikRepository $rubrikrepo): Response
    {

        $rubrik = $rubrikrepo->find($id);
        //On vérifie que la rubrique sur laquelle on a cliqué existe sinon ça renvoie une erreur
        if(!$rubrik){
            throw $this->createNotFoundException('Rubrik not found');
        }

        //Obtention des posts de chaque rubrik par date de création en DESC
        $posts = $prepo->findBy(['rubrik' => $rubrik], ['createdAt' => 'DESC'], 2);
        $posts2 = $prepo->findBy(['rubrik' => $rubrik], ['createdAt' => 'DESC'], 2, 2);

        //Rendre la vue
        return $this->render('rubrik/rubrik.html.twig', [
            'rubrik' => $rubrik,
            'posts' => $posts,
            'posts2' => $posts2,
        ]);
    }

    #[isGranted('ROLE_USER')]
    #[Route('/post/{id}', name: 'show', requirements:['id' => '\d+'])]
    public function show(Post $post, $id, Request $request, PostRepository $reppo, CommentRepository $creppo, EntityManagerInterface $emi): Response
    {
        if(!$post){
            return $this->redirectToRoute('app_post');
        }

        $posts = $reppo->find($id);
        $comments = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comments);
        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid()){
            $user = $this->getUser();
            $comments->setUser($user);
            $comments->setPost($posts);
            $comments->setCreatedAt(new \DateTimeImmutable());

            $emi->persist($comments);
            $emi->flush();

            return $this->redirectToRoute('show', ['id' => $id]);
        }

        $allComments = $creppo->findCommentByPost($id);

        return $this->render('show/show.html.twig', [
            'posts' => $posts,
            'comments' => $allComments,
            'comment_form' => $commentForm->createView(),
        ]);
    }
}
