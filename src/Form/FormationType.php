<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Playlist;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la formation',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])    
            ->add('playlist', EntityType::class, [
                'label' => 'Playlist',
                'class' => Playlist::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('video_id', FileType::class, [
                'label' => 'Vidéo (format MP4 et MKV uniquement)',
                'required' => false,
                'mapped' => false,
                'attr' => ['accept' => 'video/mp4,video/x-matroska'],
            ]) 
            ->add('categories', EntityType::class, [
                'label' => 'Catégories',
                'class' => Categorie::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
            ])    
            ->add('published_at', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(), 
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
