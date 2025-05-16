<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $commonAttr = [
            'autocomplete' => 'off',
            'oncopy' => 'return false;',
            'onpaste' => 'return false;',
            'oncut' => 'return false;',
            'oncontextmenu' => 'return false;',
            'maxlength' => 30,
//            'oninput' => "this.value = this.value.replace(/\s+/g, '');",
            'style' => 'background-image : none',
        ];

        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'ng-change' => 'ctrl.validateFields()',
                    'ng-model' => 'ctrl.nameUser',
                    'ng-class' => "{'is-invalid' : ctrl.nameUserError}"
                ]
            ])
            ->add('email', TextType::class, [
                'label' => false,
                'attr' => [
                    'ng-model' => 'ctrl.email',
//                    'oninput' => "this.value = this.value.replace(/\s+/g, '');",
                    'ng-change' => 'ctrl.validateEmail(); ctrl.validateFields()',
                    'ng-class' => "{'is-invalid' : !ctrl.validEmail || ctrl.emailError}"
                ]
            ])
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => [
                    'ng-model' => 'ctrl.username',
//                    'oninput' => "this.value = this.value.replace(/\s+/g, '');",
                    'ng-change' => 'ctrl.validateUsername(); ctrl.validateFields()',
                    'ng-class' => "{'is-invalid' : !ctrl.validUsername || ctrl.usernameError}"
                ]
            ])
            ->add('password', TextType::class, [
                'label' => false,
                'attr' => array_merge($commonAttr, [
                    'ng-model' => 'ctrl.password',
                    'ng-change' => 'ctrl.validatePassword(); ctrl.validateSamePasswords(); ctrl.validateFields()',
                    'ng-attr-type' => "{{ ctrl.showPassword['password'] ? 'text' : 'password' }}",
                    'ng-class' => "{'is-invalid' : ctrl.passwordError || !ctrl.validPassword }",
                ])
            ])
            ->add('password_confirm', TextType::class, [
                'label' => false,
                'attr' => array_merge($commonAttr, [
                    'ng-model' => 'ctrl.passwordConfirm',
                    'ng-change' => 'ctrl.validateSamePasswords(); ctrl.validateFields()',
                    'ng-attr-type' => "{{ ctrl.showPassword['passwordConfirm'] ? 'text' : 'password' }}",
                    'ng-class' => "{'is-invalid' : ctrl.passwordConfirmError}",
                ])
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'ng-submit' => 'ctrl.registerUser($event)',
            ]
        ]);
    }

}