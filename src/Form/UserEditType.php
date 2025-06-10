<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'ng-model' => 'ctrl.userEdit.name',
                    'placeholder' => 'Nombres y Apellidos del Usuario',
                    'style' => 'text-transform: uppercase;'
                ],
            ])
            ->add('password', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'ng-model' => 'ctrl.userEdit.password',
                    'placeholder' => 'Ingrese nueva contraseña',
                ],
            ])
            ->add('confirm_password', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'ng-model' => 'ctrl.userEdit.confirm_password',
                    'placeholder' => 'Confirmar contraseña',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'ng-submit' => 'submitFormEdit($event)',
            ]
        ]);
    }
}