<?php

namespace App\Form;

use App\Entity\Staff;
use App\Entity\StaffPositions;
use App\Repository\StaffPositionsRepository;
use App\Repository\StaffRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class StaffType extends AbstractType
{


    private $security;
    /**
     * @var StaffRepository
     */
    private $staffRepository;
    /**
     * @var StaffPositionsRepository
     */
    private $staffPositionsRepository;


    public function __construct(StaffRepository $staffRepository, StaffPositionsRepository $staffPositionsRepository, Security $security)
    {
        $this->security = $security;
        $this->staffRepository = $staffRepository;
        $this->staffPositionsRepository = $staffPositionsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('position', EntityType::class, [
                'class' => StaffPositions::class,
                'choice_label' => function(StaffPositions $staffPositions) {
                    return sprintf(' %s', $staffPositions->getName());
                },
                'placeholder' => 'Choose an Position',
                'choices' => $this->staffPositionsRepository->findByCompany($user->getCompany()),
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
            'data_class' => Staff::class,
        ]);
    }
}
