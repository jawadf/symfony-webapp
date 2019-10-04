<?php
namespace App\Form\Type;

use App\Entity\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;


class UploadImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('upload', FileType::class, [
            'block_name' => 'uploadimage',
            'label' => false,
            'mapped' => false,
            'required' => false,
            "attr"=>[
                "class"=>"custom-file"
            ], 
            'constraints' => [
                new File([
                    'maxSize' => '25024k', 
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid JPG file'
                ])
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }



}