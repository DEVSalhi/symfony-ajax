<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(PostRepository $repo)
    {
        return $this->render('post/index.html.twig', [
            'posts' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/post/{id}/like",name="post_like")
     * @param Post $post
     * @param EntityManagerInterface $manager
     * @param PostLikeRepository $likeRepo
     * @return Response
     */
    public function like(Post $post,EntityManagerInterface $manager,PostLikeRepository $likeRepo):Response {

        $user=$this->getUser();
        if(!$user) return $this->json(['code'=>403,'message'=>'Unauthorized'],403);
        if($post->isLikedByUser($user)){
            $like=$likeRepo->findOneBy([
                'post'=>$post,
                'user'=>$user
            ]);

            $manager->remove($like);
            $manager->flush();
            return $this->json(['code'=>200,'message'=>'like bien supprimé','likes'=>$likeRepo->count(['post'=>$post])],200);
        }

        $like=new  PostLike();
        $like->setUser($user)
            ->setPost($post);
        $manager->persist($like);
        $manager->flush();

        return $this->json(['code'=>200,'message'=>'like bien ajouté','likes'=>$likeRepo->count(['post'=>$post])],200);

    }
}
