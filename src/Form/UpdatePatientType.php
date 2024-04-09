<?php

namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UpdatePatientType extends UpdateBaseUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('picture', FileType::class, [
                'required' => false,
                'label' => 'Profile Picture',
                'data' => null,
            ])
            ->add('is_followed', CheckboxType::class, [
                'label' => 'Are you under professional care in here?',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Modify Profile',
                'attr' => ['class' => 'btn btn-primary align-right'],
            ])
            ->add('hidden_original_picture', HiddenType::class, [
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }

    public function getParent()
    {
        return PatientType::class;
    }
}
