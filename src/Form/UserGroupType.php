<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'ng-model' => 'ctrl.nameGroup',
                    'ng-class' => "{'is-invalid': ctrl.groupError}",
                    'ng-change' => 'ctrl.validateFields()',
                    'style' => 'text-transform: uppercase',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'ng-submit' => 'ctrl.registerGroup($event)',
            ]
        ]);
    }
}
