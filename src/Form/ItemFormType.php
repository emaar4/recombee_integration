<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bookID', NumberType::class, [
                'label' => 'bookID',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'title',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('authors', TextType::class, [
                'label' => 'authors',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('averageRating', NumberType::class, [
                'label' => 'average rating',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('isbn', TextType::class, [
                'label' => 'isbn',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('isbn13', TextType::class, [
                'label' => 'isbn13',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('languageCode', TextType::class, [
                'label' => 'language code',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('numPages', NumberType::class, [
                'label' => 'num pages',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('ratingsCount', NumberType::class, [
                'label' => 'ratings count',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('textReviewsCount', NumberType::class, [
                'label' => 'text reviews count',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('publicationDate', TextType::class, [
                'label' => 'publication date',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('publisher', TextType::class, [
                'label' => 'publisher',
                'constraints' => [
                    new NotBlank()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class
        ]);
    }
}
