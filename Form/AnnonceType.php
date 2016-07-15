<?php

namespace MonApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AnnonceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            ->add('description', TextareaType::class)
            ->add('prix', TextType::class, array('required' => false))
            ->add('villes', TextType::class)
            ->add('categories', EntityType::class, array(
                'class'        => 'MonApiBundle:Categories',
                'choice_label' => 'name',
            ))
            ->add('images', CollectionType::class, array('entry_type' => ImagesType::class, 'allow_add' => true, 'allow_delete' => true))
            ->add('save', SubmitType::class, array('label' => 'Envoyer'));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MonApiBundle\Entity\Annonce'
        ));
    }
}
