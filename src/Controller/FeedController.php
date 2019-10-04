<?php

namespace App\Controller;

use App\Form\Type\PostType;
use App\Form\Type\DeleteType;
use App\Entity\Post;
use App\Entity\Image;
use App\Entity\Category;
use App\Service\ImagesUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image as Img;
use Symfony\Component\Validator\Constraints\File;


class FeedController extends AbstractController
{ 
    /**
     * @Route("/feed", name="feed")
     */ 
    public function index(Request $request, ImagesUploader $imagesUploader)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Show previous posts
        $previousPosts = $this->getDoctrine()
                        ->getRepository(Post::class)
                        ->findAll();


        return $this->render('feed/index.html.twig', [
            'posts' => $previousPosts
        ]);
    }

    /**
     * @Route("/new", name="new")
    */ 
    public function new(Request $request, ImagesUploader $imagesUploader) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Get User object
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

       
        // Generate new Post, to pass it later to the Form
        $post = new Post();

        // Relate this Post to the current user
        $post->setUser($user);

        // Optional, just so that one upload field is generated at first
        $newImage = new Image();
        $post->addImage($newImage);

        // Create the form (of PostType) and pass it the Post entity
        $form = $this->createForm(PostType::class, $post);

        // Listen for changes and handle any request
        $form->handleRequest($request);

        // Check if form is submitted
        if($form->isSubmitted() && $form->isValid()) {

            // Get the images that are now available on the Post entity
            $images = $post->getImage();
            $uploaded = $request->files->get('post')["image"][0];

            //dump($images);

            if($images && $uploaded) {
                // Upload the image using a Service, check notes in ImagesUploader.php 
                $imagesUploader->upload($request, $images);
            }
            
            /*
            if($images) {

                // Get the array of uploaded images
                $uploaded = $request->files->get('post')["image"];

                // Turn this array into a 0 based array (in order to loop through it along with another 0 based array: $images)
                $uploadedArray = array();
                foreach($uploaded as $element) {
                    array_push($uploadedArray, $element);
                }

                for($i=0; $i<=count($images)-1; $i++) {

                    // Get one uploaded image
                    $theImage = $uploadedArray[$i]["upload"];

                    // Validate image
                    if(!array_search($theImage->guessExtension(), ['jpg', 'jpeg', 'png'])) {
                        throw new Exception('Not a jpeg or png');
                    }
                    $validator = Validation::createValidator();
                    $violations = $validator->validate($theImage, [
                        new Img([
                            'maxWidth' => '1500', 
                            'maxHeight' => '1000',
                            'detectCorrupted' => true,
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid JPG file'
                        ]),
                    ]);

                    // Get the filename without the extension
                    //$originalFilename = pathinfo($theImage->getClientOriginalName(), PATHINFO_FILENAME);

                    // generate unique ID + guess the extension
                    $newFilename = uniqid().'.'.$theImage->guessExtension();

                    // set this name into Image->filename
                    $images[$i]->setFilename($newFilename);

                    // Move file to 'public/img' directory
                    $imagesDirectory = $this->getParameter('images_directory');
                    try {
                        $theImage->move(
                            $imagesDirectory,
                            $newFilename
                        );
                    } catch (FileException $e) {
                                echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }         
                }

                // for debugging
                // dump($images);

                // JUST TO SEE THEM IN DUMP SERVER
               // $categories = $post->getCategories();
               // dump($categories);

            } */
            
            // Submit the post to the DB using Doctrine
            $post = $form->getData();
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush(); 

            return $this->redirectToRoute('feed');
        }

        return $this->render('feed/new.html.twig', [
            'form' => $form->createView()
        ]);

    }
    
    /**
     * @Route("/feed/post/{id}/edit", name="edit")
     */ 
    public function edit(Post $post, Request $request, ImagesUploader $imagesUploader, $id) {

        // check for "edit" access: calls all voters
        $this->denyAccessUnlessGranted('edit', $post);

        $entityManager = $this->getDoctrine()->getManager();

        if (null === $post = $entityManager->getRepository(Post::class)->find($id)) {
            throw $this->createNotFoundException('No post found for id '.$id);
        }
        

        $originalImages = new ArrayCollection();

        // Create an ArrayCollection of the current Image objects in the database 
        foreach ($post->getImage() as $image) {
        $originalImages->add($image);
        }

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $filesystem = new Filesystem();

            // remove the relationship between the tag and the Task
            foreach ($originalImages as $image) {
                if (false === $post->getImage()->contains($image)) {
                    // remove the Task from the Tag
                    //$image->getPost()->removeImage($post);

                    // if it was a many-to-one relationship, remove the relationship like this
                    // $image->setPost(null);

                    $entityManager->persist($image);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $entityManager->remove($image);

                    $imagesDirectory = $this->getParameter('images_directory');
                    unlink($imagesDirectory.'/'.$image->getFilename());
                }
        
            }

            // Get the images that are now available on the Post entity
            $images = $post->getImage();

            if($images) {
                // Upload the image using a Service, check notes in ImagesUploader.php 
                $imagesUploader->upload($request, $images);
            }

           // $post = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('edit', [
                'id' => $post->getId(),
            ]);
        }

        return $this->render('feed/edit.html.twig', [
            'form' => $form->createView(),
            'images' => $originalImages,
        ]);

    }

    // public function view() {

    // }


    /**
     * @Route("/feed/post/{id}/delete", name="delete")
     */ 
    public function delete($id, Post $post, Request $request, ImagesUploader $imagesUploader, EntityManagerInterface $entityManager) {
        // check for "edit" access: calls all voters
        //$this->denyAccessUnlessGranted('edit', $post);

        $form = $this->createForm(DeleteType::class, $post);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
            
            return $this->redirectToRoute('feed');
        }

        return $this->render('feed/delete.html.twig', [
            'form' => $form->createView()
        ]);

    }

}