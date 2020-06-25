<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\Treatment;
use App\Repository\EquipmentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TreatmentType extends AbstractType
{
    /**
     * @var EquipmentRepository
     */
    private $equipmentRepository;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EquipmentRepository $equipmentRepository, Security $security)
    {
        $this->equipmentRepository = $equipmentRepository;
        $this->security = $security;

    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add('name')
            ->add('cost')
            ->add('timeToComplete')
            ->add('equipment', EntityType::class, [
                'class' => Equipment::class,
                'choice_label' => function(Equipment $equipment) {
                    return sprintf(' %s', $equipment->getName());
                },
                'placeholder' => 'Choose an Equipment',
                'choices' => $this->equipmentRepository->findByCompany($user->getCompany()),
                'required'=>false,
                'label'=>'Equipment (Optional)'
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
            'data_class' => Treatment::class,
        ]);
    }
}
