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
                'label' => "Specialization <small>-> <span style='color:orange'>Maintain 
                            <b style='color:lightgreen'>Ctrl</b> to select multiple</span></small>",
                'required' => true,
                'multiple' => true,
                'label_html' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'specialization-select',
                ],
            ])
            ->add('other_specialization_checkbox', CheckboxType::class, [
                'label' => "<small>Specialization not listed? </small>",
                'required' => false,
                'mapped' => false,
                'label_html' => true,
                'attr' => [
                    'class' => 'other-specialization-checkbox',
                    'style' => 'transform: scale(0.8);',
                ],
            ])
            ->add('other_specialization', TextType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'other-specialization-field w-full border rounded mb-2 p-2',
                    'style' => 'display: none;',
                    'placeholder' => 'Add your specialization',
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
            'label' => "Youtube Video Link <small>-><span title='Paste Format: \"https://www.youtube.com/watch?v=...\"' style='cursor:pointer'>❔</span></small>",
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
