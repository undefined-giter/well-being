<?php

namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', null, ['label' => 'first name'])
            ->add('last_name', null, ['label' => 'last name'])
            ->add('email', null)
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Password'
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => 'Profile Picture',
                'required' => false,
                'mapped' => false,
            ])
            ->add('description')
            // ->add('slug')
            // ->add('roles', null)
            // ->add('registration_date', null, ['widget' => 'single_text'])
            // ->add('is_followed')
        ;

        $builder->add('save', SubmitType::class, [
            'label' => 'Register As Patient',
            'attr' => ['class' => 'btn btn-primary'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
