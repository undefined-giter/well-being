<?php

namespace App\Form;

use App\Entity\Patient;
use App\Form\BaseUserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

        $builder
        ->add('interestedIn', ChoiceType::class, [
            'choices' => [
                'Acupuncturist' => 'Acupuncturist',
                'Addiction Counselor' => 'Addiction Counselor',
                'Aromatherapist' => 'Aromatherapist',
                'Art Therapist' => 'Art Therapist',
                'Chiropractor' => 'Chiropractor',
                'Cognitive-Behavioral Therapist' => 'Cognitive-Behavioral Therapist',
                'Emotion Management Therapist' => 'Emotion Management Therapist',
                'Energy Specialist' => 'Energy Specialist',
                'Family and Couple Therapist' => 'Family and Couple Therapist',
                'Herbalist' => 'Herbalist',
                'Hypnotherapist' => 'Hypnotherapist',
                'Life Coach' => 'Life Coach',
                'Meditation Therapist' => 'Meditation Therapist',
                'Mental Health Counselor' => 'Mental Health Counselor',
                'Mindfulness Therapist' => 'Mindfulness Therapist',
                'Massage Therapist' => 'Massage Therapist',
                'Music Therapist' => 'Music Therapist',
                'Naturopath' => 'Naturopath',
                'Osteopath' => 'Osteopath',
                'Primitive Reflex Therapist' => 'Primitive Reflex Therapist',
                'Psychiatrist' => 'Psychiatrist',
                'Psychologist' => 'Psychologist',
                'Psychotherapist' => 'Psychotherapist',
                'Reiki Practitioner' => 'Reiki Practitioner',
                'Reflexologist' => 'Reflexologist',
                'Relaxation Therapist' => 'Relaxation Therapist',
                'Sexologist' => 'Sexologist',
                'Sophrologist' => 'Sophrologist',
                'Speech Therapist' => 'Speech Therapist',
                'Stress Management Therapist' => 'Stress Management Therapist',
            ],
            'label' => "Yours interests <small>-> <span style='color:orange'>Maintain 
                        <b style='color:lightgreen'>Ctrl</b> to select multiple</span></small>",
            'required' => false,
            'multiple' => true,
            'label_html' => true,
            'mapped' => false,
            'attr' => ['class' => 'block'],
        ])
        ->add('save', SubmitType::class, [
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
