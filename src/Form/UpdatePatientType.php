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
            ->add('delete_picture', CheckboxType::class, [
                'label' => 'Delete current profile picture',
                'mapped' => false,
                'required' => false,
            ])
            ->add('delete_interestedIn', CheckboxType::class, [
                'label' => 'Delete current profile picture',
                'mapped' => false,
            ])
            ->add('is_followed', CheckboxType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Modify Profile',
                'attr' => ['class' => 'align-right btn bg-blue-700 hover:bg-blue-800 text-slate-200 hover:text-slate-100'],
            ])
            ->add('hidden_original_picture', HiddenType::class, [
                'mapped' => false,
            ])->add('hidden_original_interests', HiddenType::class, [
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
