<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('role', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'ng-model' => 'ctrl.role',
                    'id' => 'role',
                    'ng-class' => "{'is-invalid': ctrl.roleError}",
                    'ng-change' => 'ctrl.validateFields()',
                    'style' => 'text-transform: uppercase',
                ],

            ])
            ->add('description', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'ng-model' => 'ctrl.description',
                    'id' => 'description',
                    'ng-class' => "{'is-invalid' : ctrl.descriptionError}",
                    'ng-change' => 'ctrl.validateFields()'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'ng-submit' => 'ctrl.registerRole($event)',
            ]
        ]);
    }
}
