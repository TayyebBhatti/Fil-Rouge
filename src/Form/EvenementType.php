<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

final class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false])
            ->add('dateDebut', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Date de début'])
            ->add('dateFin', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Date de fin'])
            ->add('capaciteMax', IntegerType::class, ['label' => 'Capacité max'])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'required' => false,
            ])
            ->add('lieu', LieuType::class, [
                'label' => 'Lieu',
                'required' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (optionnel)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                        'mimeTypesMessage' => 'Formats acceptés: JPG, PNG, WEBP, GIF',
                    ]),
                ],
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
            ->add('nouvelleCategorie', TextType::class, [
                'label' => 'Nouvelle catégorie (optionnel)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Concert, Sport, LAN…',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Evenement::class]);
    }
}
