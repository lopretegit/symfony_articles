<?php


namespace App\Controller;

use App\Entity\Post;

 
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class PostController extends Controller
{

    /**
     * @Route("/posts",name="show_posts")
     * @Method({"GET"})
     */
    public function showPosts()
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repository->findAll();

        if (!count($posts)){
            $response=array(
                'message'=>'No posts found!'
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

   
        $postsArray = array();
        foreach ($posts as $post) {
            $postArray = array();
            $postArray['id']=$post->getId();
            $postArray['title']=$post->getTitle();
            $postArray['description']=$post->getDescription();
            $postsArray[]=$postArray;
        }

         $response = new JsonResponse($postsArray, 200);
         return $response;
    }

    /**
     * @Route("/post/{id}",name="show_post")
     * @Method({"GET"})
     */
    public function showPost($id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if (empty($post)) {
            $response=array(
            'message'=>'post Not found !'
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $postArray = array();
        $postArray['id']=$post->getId();
        $postArray['title']=$post->getTitle();
        $postArray['description']=$post->getDescription();
       
        $response = new JsonResponse($postArray, 200);
        return $response;
    }


    /**
    * @Route("/insertpost/{title}/{description}", name="insert_post")
    * @Method({"POST"})
    */

    public function inserePost($title = "", $description = "")
    {
        if(strlen($title)>0)
        {
            $post = new Post();
            $post->setTitle($title);
            $post->setDescription($description);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $postArray = array();
            $postArray['id']=$post->getId();

            $postArray['message']=
            'Post ' . $postArray['id'] . ' created';
           

            $response = new JsonResponse($postArray, Response::HTTP_CREATED);
            return $response;
        }
        else
        {
            throw new BadRequestHttpException('Title missing', null, 400);
            
        }    
    }


     /**
     * @Route("/deletepost/{id}", name = "delete_post")
     * @Method({"DELETE"})
     */
    public function deletePost($id)
     {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if (empty($post)) {
            $response=array(
            'message'=>'post Not found !'
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
    
        $postArray = array();
        $postArray['id']=$post->getId();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        $response=array(
            'message' => 'Post ' . $postArray['id'] . ' deleted'
           ); 

        $response = new JsonResponse($response,200);
        return $response;
    }

    
}