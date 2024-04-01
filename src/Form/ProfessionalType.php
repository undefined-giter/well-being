<?php

namespace App\Form;

use App\Entity\Professional;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProfessionalType extends AbstractType
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
            ->add('specialization', ChoiceType::class, [
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
                'label' => "Specialization -> <small style='color:green'>Maintain 
                            <b style='color:brown'>Ctrl</b> to select multiple</small>",
                'required' => true,
                'multiple' => true,
                'label_html' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'specialization-select',
                ],
            ])
            ->add('other_specialization_checkbox', CheckboxType::class, [
                'label' => '<small>Specialization not listed ?</small>',
                'required' => false,
                'mapped' => false,
                'label_html' => true,
                'attr' => [
                    'class' => 'other-specialization-checkbox',
                ],
            ])
            ->add('other_specialization', TextType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'other-specialization-field w-full',
                    'style' => 'display: none;',
                    'placeholder' => 'Enter your specialization',
                ],
            ])
            ->add('location', CollectionType::class, [
                'label' => false,
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ])
            ->add('online_availability', CheckboxType::class, [
                'data' => true,
            ])
            ->add('irl_availability', CheckboxType::class, [
                'label' => '<p id="irl_availability_label" style="color:orange; text-align:right">Uncheck if no place</p>',
                'required' => false,
                'mapped' => false,
                'label_html' => true,
                'data' => true,
                'attr' => [
                    'class' => 'irl_availability-checkbox',
                ],
            ])
            ->add('video')
            // ->add('roles', null)
            // ->add('slug')
            // ->add('registration_date', null, ['widget' => 'single_text'])
        ;

        $builder->add('save', SubmitType::class, [
            'label' => 'Register as Professional',
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $irlAvailability = $form->get('irl_availability')->getData();
            $onlineAvailability = $form->get('online_availability')->getData();

            if (!$irlAvailability && !$onlineAvailability) {
                $form->addError(new FormError('Select at least one availability option, your patients needs you.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Professional::class,
        ]);
    }
}
