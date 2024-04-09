<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

abstract class BaseUserType extends AbstractType
{
    protected function addCommonFields(FormBuilderInterface $builder)
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'First Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name'
                    ])
                ]
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Last Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email address'
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => true,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password'
                    ])
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => "Profile Picture <small>-> <span style='color:orange'>.jpg or .png</span></small>",
                'label_html' => true,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PNG or JPEG format under 2Mo.'
                    ])
                ],
            ])
            ->add('description');
    }
}
