<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false])
            ->add('dateDebut', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Date de début'])
            ->add('dateFin', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Date de fin', 'required' => false])
            ->add('capaciteMax', IntegerType::class, ['label' => 'Capacité max', 'required' => false])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'required' => false,
            ])
            // Ici on permet de créer ou modifier un Lieu librement
            ->add('lieu', LieuType::class, [
                'label' => 'Lieu',
                'required' => false,
            ])
            ->add('image', TextType::class, [
                'label' => 'Chemin image (ex: img/1.jpg)',
                'required' => false,
                'attr' => ['placeholder' => 'img/1.jpg'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Evenement::class]);
    }
}
