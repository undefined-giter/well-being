<?php

namespace App\Form;

use App\Entity\Patient;
use App\Form\BaseUserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class PatientType extends BaseUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::addCommonFields($builder, $options);
        // $builder
            // ->add('first_name')
            // ->add('last_name')
            // ->add('email')
            // ->add('password')
            // ->add('picture')
            // ->add('description')

            // ->add('slug')
            // ->add('roles', null)
            // ->add('registration_date')
            // ->add('is_followed')
        // ;

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
