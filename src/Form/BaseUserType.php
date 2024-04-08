<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

abstract class BaseUserType extends AbstractType
{
    protected function addCommonFields(FormBuilderInterface $builder)
    {
        $builder
            ->add('first_name', TextType::class, ['label' => 'First Name'])
            ->add('last_name', TextType::class, ['label' => 'Last Name'])
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Password'
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
