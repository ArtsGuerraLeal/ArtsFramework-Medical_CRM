<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\State;
use App\Repository\StateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AddressType extends AbstractType
{

    /**
     * @var StateRepository
     */
    private $stateRepository;
    /**
     * @var Security
     */
    private $security;

    public function __construct(StateRepository $stateRepository, Security $security)
    {
        $this->stateRepository = $stateRepository;
        $this->security = $security;

    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('line1', TextType::class,['label'=>'Calle','required'=>false,])
            ->add('line2', TextType::class,['label'=>'Colonia','required'=>false,])
            ->add('postalCode', NumberType::class,['label'=>'Codigo Postal','required'=>false,])
            ->add('city', TextType::class,['label'=>'Ciudad','required'=>false,])

            ->add('state',EntityType::class, [
                'class' => State::class,
                'choice_label' => function(State $state) {
                    return sprintf(' %s', $state->getName());
                },
                'placeholder' => 'Escoger Estado ...',
                'choices' => $this->stateRepository->findAll(),
                'required'=>false,
                'label'=>'Estado'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
