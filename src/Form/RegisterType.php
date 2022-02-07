<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',TextType::class,[
                'label' => 'Votre prénom',
                'constraints' => new length(0, 2, 30),
                'constraints' => new Regex([
                    'pattern' => '/\d/',
                    'match' => false,
                    'message' => 'Votre prénom ne peut pas contenir de nombre',
                ]),
                    'required'=> true,
                'attr' => [
                    'placeholder' => 'Merci de saisir votre prénom'
                ]
            ])
            ->add('lastname',TextType::class,[
                'label' => 'Votre nom',
                'constraints' => new length(0, 2, 30),
                'constraints' => new Regex([
                    'pattern' => '/\d/',
                    'match' => false,
                    'message' => 'Votre nom ne peut pas contenir de nombre',
                ]),
                    'required'=> true,
                'attr' => [
                    'placeholder' => 'Merci de saisir votre nom'
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'Votre email',
                'constraints' => new length(0, 2, 60),
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse email'
                    ]
            ])
            ->add('password', RepeatedType::class,[
                'type'=> PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique.',
                'label' => 'Votre mot de passe',
                'constraints' => new Regex([
                    'pattern' => "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{0,4096}$/",
                    'message' => 'Le mot de passe doit contenir obligatoirement une minuscule, une majuscule, un chiffre et un caractère spécial.',
                ]),
                'required' => true,
                'first_options' => [ 'label' => 'Mot de passe','attr' => [
                    'placeholder' => 'Merci de saisir votre mot de passe'
                ]],
                'second_options' => [ 'label' => 'Confirmez votre mot de passe', 'attr' => [
                    'placeholder' => 'Merci de confirmer votre mot de passe'
                ]]


            ])
//            ->add('password_confirm', PasswordType::class,[
//                'label' => 'Confirmez votre mot de passe',
//                'mapped' => false,
//                'attr' => [
//                    'placeholder' => 'Confirmez votre mot de passe'
//                ]
//            ])
            ->add('submit', SubmitType::class,[
                'label' => "S'inscrire",
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
