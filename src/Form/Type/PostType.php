<?php
namespace App\Form\Type;

use App\Entity\Post;
use App\Entity\Category;
use App\Form\Type\UploadImageType;
use App\Form\Type\CategoryType;
use Symfony\Component\OptionsResolver\OptionsResolver;
 
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class ,[
             'block_name' => 'form_text',
           //  "attr"=>["class"=>"test-class"]
             ])
        ->add('description', TextType::class ,[
            'block_name' => 'form_description',
            ])
        ->add('categories', EntityType::class, [
            // looks for choices from this entity
            'class' => Category::class,
            
            'choice_label' => 'name',

            // used to render check boxes
            'multiple' => true,
            'expanded' => true,
        ])
        ->add('image', CollectionType::class, [
            'entry_type' => UploadImageType::class,
            'label' => 'Upload Images',
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'by_reference' => false, 
        ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

    
}