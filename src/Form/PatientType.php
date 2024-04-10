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
                'Acupuncturist' => 'acupuncturist',
                'Addiction counselor' => 'addiction_counselor',
                'Aromatherapist' => 'aromatherapist',
                'Art therapist' => 'art_therapist',
                'Chiropractor' => 'chiropractor',
                'Cognitive-behavioral therapist (CBT)' => 'cognitive_behavioral_therapist',
                'Emotion management therapist' => 'emotion_management_therapist',
                'Energy specialist' => 'energy_specialist',
                'Family and couple therapist' => 'family_and_couple_therapist',
                'Herbalist' => 'herbalist',
                'Hypnotherapist' => 'hypnotherapist',
                'Life coach' => 'life_coach',
                'Meditation therapist' => 'meditation_therapist',
                'Mental health counselor' => 'mental_health_counselor',
                'Mindfulness therapist' => 'mindfulness_therapist',
                'Massage therapist' => 'massage_therapist',
                'Music therapist' => 'music_therapist',
                'Naturopath' => 'naturopath',
                'Osteopath' => 'osteopath',
                'Primitive reflex therapist' => 'primitive_reflex_therapist',
                'Psychiatrist' => 'psychiatrist',
                'Psychologist' => 'psychologist',
                'Psychotherapist' => 'psychotherapist',
                'Reiki practitioner' => 'reiki_practitioner',
                'Reflexologist' => 'reflexologist',
                'Relaxation therapist' => 'relaxation_therapist',
                'Sexologist' => 'sexologist',
                'Sophrologist' => 'sophrologist',
                'Speech therapist' => 'speech_therapist',
                'Stress management therapist' => 'stress_management_therapist',
            ],
            'label' => "Yours interests <small>-> <span style='color:orange'>Maintain 
                        <b style='color:green'>Ctrl</b> to select multiple</span></small>",
            'required' => true,
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
