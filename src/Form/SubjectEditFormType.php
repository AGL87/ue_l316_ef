<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubjectEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class, [
                "label" => "Titre",
                "required" => true,
                "constraints" => [
                    new NotBlank(),
                    new Length(min: 1, max: 75)
                ]
            ])
            ->add("content", TextareaType::class, [
                "label" => "Contenu",
                "required" => true,
                "constraints" => [
                    new NotBlank(),
                    new Length(min: 1)
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
