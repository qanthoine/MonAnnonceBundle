<?php

namespace MonApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ImagesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, array('label' => "Fichier :  ", 'required' => true, 'constraints' => [new File(['mimeTypes' => ['image/jpg', 'image/jpeg', 'image/jpe', 'image/bmp', 'image/wbmp', 'image/dib', 'image/png'],'mimeTypesMessage' => "Formats autorisés : JPG / JPEG / JPE / BMP / WBMP / PNG", 'maxSize' => '5M', 'maxSizeMessage' => "Les images ne peuvent dépasser 5mo"])]));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MonApiBundle\Entity\Images'
        ));
    }
}
