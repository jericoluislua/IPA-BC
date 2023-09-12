<?php

namespace App\Form;

use App\Repository\SocialMediaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Form that will be used to create or edit employee data. The fields will be validated with symfony's Validator bundle.
 * Author: Jerico Lua(+ help of make bundle)
 */
class EmployeeFormType extends AbstractType
{

    private SocialMediaRepository $socialMediaRepository;

    /**
     * @param SocialMediaRepository $socialMediaRepository
     */
    public function __construct(SocialMediaRepository $socialMediaRepository)
    {
        $this->socialMediaRepository = $socialMediaRepository;
    }

    /**
     * Builds the whole form with fields: first_name, last_name, email, mobile, telephone, role and Social Media Platforms
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $socialMediaPlatforms = $this->socialMediaRepository->findAll();

        $builder
            ->add('first_name', TextType::class, [
                'label' => 'Vorname',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Geben Sie Ihren Vornamen ein.'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Das Vorname Feld darf nicht leer sein."
                    ])
                ]
            ])
            ->add('last_name', TextType::class,[
                'label' => 'Nachname',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Geben Sie Ihren Nachnamen ein.'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Das Nachname Feld darf nicht leer sein."
                    ])
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'E-Mail',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Beispiel: max.mustermann@artd.ch'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Das E-Mail Feld darf nicht leer sein."
                    ]),
                    new Email([
                        'message' => "Die eingegebene E-Mail ist ungültig. Bitte prüfen Sie, ob ein @-Symbol vorhanden ist."
                    ])
                ]
            ])
            ->add('mobile', TextType::class,[
                'required' => true,
                'label' => '+41' /*this is the inline prefix*/,
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ],
                'attr' => [
                    'placeholder' => '79 123 45 67 (persönlich)'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Das Mobile Feld darf nicht leer sein."
                    ]),
                    new Regex([
                        'message' => 'Die Mobilenummer ist ungültig. Beispiel: 79 123 45 67',
                        'pattern' => '/[0-9]{2,3}[^\S\t\n\r][0-9]{3}[^\S\t\n\r][0-9]{2}[^\S\t\n\r][0-9]{2}$/' /*Only allow numbers with syntax of "79 123 32 12"*/
                    ])
                ]
            ])
            ->add('telephone', TextType::class,[
                /*The real label will be defined in the twig files, as it isn't possible to set it here.*/
                'required' => true,
                'label' => '+41' /*this is the inline prefix*/,
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ],
                'attr' => [
                    'placeholder' => '79 123 45 67 (geschäftlich)'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Das Telefon Feld darf nicht leer sein."
                    ]),
                    new Regex([
                        'message' => 'Die Telefonnummer ist ungültig. Beispiel: 79 123 45 67',
                        'pattern' => '/[0-9]{2,3}[^\S\t\n\r][0-9]{3}[^\S\t\n\r][0-9]{2}[^\S\t\n\r][0-9]{2}$/' /*Only allow numbers with syntax of "79 123 32 12"*/
                    ])
                ]
            ])
            ->add('role', TextType::class,[
                'label' => 'Funktion',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Beispiel: Applikationsentwickler'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Das Funktion Feld darf nicht leer sein."
                    ])
                ]
            ])
        ;
        foreach ($socialMediaPlatforms as $socialMediaPlatform){
            $builder->add(strtolower($socialMediaPlatform->getPlatform()), TextType::class, [
                'required' => false,
                'row_attr' => [
                    'class' => 'input-group mb-3'
                ],
                'label' => $socialMediaPlatform->getLink(),
                'attr' => [
                    'placeholder' => 'Geben Sie Ihr ' . $socialMediaPlatform->getPlatform() . ' Profil ein.'
                ]
            ]);
        }
        $builder->add('save', SubmitType::class, ['label' => 'SPEICHERN']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
