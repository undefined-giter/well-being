<?php

namespace App\Form;
use App\Form\BaseUserType;
use App\Entity\Professional;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProfessionalType extends BaseUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::addCommonFields($builder, $options);
        
        $builder
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
                'label' => "Specialization <small>-> <span style='color:orange'>Maintain 
                            <b style='color:green'>Ctrl</b> to select multiple</span></small>",
                'required' => true,
                'multiple' => true,
                'label_html' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'specialization-select',
                ],
            ])
            ->add('other_specialization_checkbox', CheckboxType::class, [
                'label' => "<small>Specialization not listed ? </small>",
                'required' => false,
                'mapped' => false,
                'label_html' => true,
                'attr' => [
                    'class' => 'other-specialization-checkbox',
                    'style' => 'transform: scale(0.8);'
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
            ->add('location', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('online_availability', CheckboxType::class, [
                'data' => true,
                'label' => 'Online availability '
            ])
            ->add('irl_availability', CheckboxType::class, [
                'label' => '<p id="irl_availability_label" style="color:orange; text-align:right">Uncheck if no place </p>',
                'required' => false,
                'mapped' => false,
                'label_html' => true,
                'data' => true,
                'attr' => [
                    'class' => 'irl_availability-checkbox',
                ],
            ])
            ->add('video', TextType::class, [
            'label' => "Youtube video link <small>-><span title='Paste type \"https://www.youtube.com/watch?v=...\"' style='cursor:pointer'>❔</span></small>",
            'label_html' => true,
            ])
        ;

        $builder->add('save', SubmitType::class, [
            'label' => "Register as Professional",
        ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $specialization = $form->get('specialization')->getData();
            $otherSpecialization = $form->get('other_specialization')->getData();

            if (empty($specialization) && empty($otherSpecialization)) {
                $form->addError(new FormError('Please select a specialization or enter your specialization.'));
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $irlAvailability = $form->get('location')->getData();
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
