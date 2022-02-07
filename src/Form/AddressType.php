<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Quel nom souhaitez-vous donner à votre adresse ?',
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Nommez votre adresse'
                ]
            ])
            ->add('firstname',TextType::class,[
                'label' => 'Votre prénom',
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Entrez votre prénom'
                ]
            ])
            ->add('lastname',TextType::class,[
                'label' => 'Votre nom',
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Entrez votre nom'
                ]
            ])
            ->add('company',TextType::class,[
                'label' => 'Votre société',
                'required' => false,
                'attr' => [
                    'placeholder' => '(facultatif) Entrez le nom de votre société'
                ]
            ])
            ->add('address',TextType::class,[
                'label' => 'Votre adresse',
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Votre adresse'
                ]
            ])
            ->add('postal',TextType::class,[
                'label' => 'Votre code postal',
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Entrez votre code postal'
                ]
            ])
            ->add('city',TextType::class,[
                'label' => 'Votre ville',
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Entrez votre ville'
                ]
            ])
            ->add('country',CountryType::class,[
                'label' => 'Votre pays',
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Entrez votre pays'
                ]
            ])
            ->add('phone',TelType::class,[
                'label' => 'Votre téléphone',
                'required'=> true,
                'attr' => [
                    'placeholder' => 'Entrez votre téléphone'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Valider',
                'attr' => [
                  'class' => 'btn-block btn-info mt-3'
    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
