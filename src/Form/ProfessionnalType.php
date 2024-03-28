<?php

namespace App\Form;

use App\Entity\Professional;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessionnalType extends AbstractType
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
            ])
            ->add('description')
            ->add('specialization')
            ->add('location')
            ->add('online_availability')
            ->add('video')
            // ->add('roles', null)
            // ->add('slug')
            // ->add('registration_date', null, ['widget' => 'single_text'])
        ;

        $builder->add('save', SubmitType::class, [
            'label' => 'Save',
            'attr' => ['class' => 'btn btn-primary'],
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Professional::class,
        ]);
    }
}
