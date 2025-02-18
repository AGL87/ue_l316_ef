<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("email", EmailType::class, [
                "label" => "Email",
                "required" => true,
                "constraints" => [
                    new Length(
                        min: 1,
                        minMessage: "Votre adresse mail ne peut pas être vide",
                        max: 255,
                        maxMessage: "Votre adresse mail ne peut pas dépasser les 255 caractères"
                    ),
                ]
            ])
            ->add("plainPassword", RepeatedType::class, [
                "mapped" => false,
                "type" => PasswordType::class,
                "first_options" => [
                    "constraints" => [
                        new NotBlank(),
                        new Length(
                            min: 1,
                            max: 255,
                        )
                    ],
                    "label" => "Mot de passe"
                ],
                "second_options" => [
                    "label" => "Répéter le mot de passe",
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
