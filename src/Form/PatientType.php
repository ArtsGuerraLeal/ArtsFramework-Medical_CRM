<?php

namespace App\Form;


use App\Entity\MaritalStatus;
use App\Entity\Patient;
use App\Repository\MaritalStatusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PatientType extends AbstractType
{
    /**
     * @var MaritalStatusRepository
     */
    private $maritalStatusRepository;
    /**
     * @var Security
     */
    private $security;

    public function __construct(MaritalStatusRepository $maritalStatusRepository, Security $security)
    {
        $this->maritalStatusRepository = $maritalStatusRepository;
        $this->security = $security;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class,['label'=>'Nombre'])
            ->add('lastName', TextType::class,['label'=>'Apellido'])
            ->add('gender',ChoiceType::class,[
                'choices' => [
                    'Male' =>'M',
                    'Female' =>'F'
                    ],
                'label'=> 'Genero'


                ])
            ->add('birthdate',BirthdayType::class, [
                        'placeholder'=>['year'=>'','month'=>'', 'day'=>''],
                    'required'=>false,
                'empty_data' => null,
                 'label'=> 'Fecha de Nacimiento'
                ])

            ->add('email',EmailType::class,['required'=>false])
            ->add('phone', TextType::class,['label'=>'Telefono',
                'required'=>false,])
            ->add('religion', TextType::class,['label'=>'Religion',
                'required'=>false,])

            ->add('maritalStatus', EntityType::class, [
                'class' => MaritalStatus::class,
                'choice_label' => function(MaritalStatus $maritalStatus) {
                    return sprintf(' %s', $maritalStatus->getName());
                },
                'placeholder' => 'Escoger Estado Civil...',
                'choices' => $this->maritalStatusRepository->findAll(),
                'required'=>false,
                'label'=>'Estado Civil'
            ])

            ->add('address',CollectionType::class,[
                'entry_type'=>AddressType::class,
                'entry_options'=>[
                    'label' => false
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ])
            ->add('attachment',FileType::class , [
                'mapped' => false,
                'required' => false,
            ])

            ->add('save',SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
