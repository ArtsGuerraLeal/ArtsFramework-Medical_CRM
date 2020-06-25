<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Treatment;
use App\Repository\TreatmentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AppointmentType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var TreatmentRepository
     */
    private $treatmentRepository;

    public function __construct(TreatmentRepository $treatmentRepository, Security $security)
    {
        $this->treatmentRepository = $treatmentRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add('title')
            ->add('beginAt',DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['id' => 'result'],

            ])
            ->add('endAt',DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['id' => 'result'],

            ])
            ->add('treatment', EntityType::class, [
                'class' => Treatment::class,
                'choice_label' => function(Treatment $treatment) {
                    return sprintf(' %s', $treatment->getName());
                },
                'placeholder' => 'Choose an Treatment',
                'choices' => $this->treatmentRepository->findByCompany($user->getCompany()),
            ])
            ->add('color')


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
