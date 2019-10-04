<?php
namespace App\Form\Type;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class,[
            'attr' => ['class' => 'form-control']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    

}

/*

 ->add('name', ChoiceType::class, [
                'choices' => [
                    new Category(),
                    new Category(),
                    new Category(),
                    new Category(),
                    new Category(),
                ],
                'choice_label' => function(Category $category, $key, $value) {
                    return strtoupper($category->getName());
                },
                'choice_attr' => function(Category $category, $key, $value) {
                    return ['class' => 'category_'.strtolower($category->getName())];
                },
                'group_by' => function(Category $category, $key, $value) {
                    // randomly assign things into 2 groups
                    return rand(0, 1) == 1 ? 'Group A' : 'Group B';
                },
                'preferred_choices' => function(Category $category, $key, $value) {
                    return $category->getName() == 'Cat2' || $category->getName() == 'Cat3';
                },
                'expanded' => true,
                'multiple' => true,
            ])




*/