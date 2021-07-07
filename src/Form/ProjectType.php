<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Project;
use App\Repository\ClientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{

    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var Security
     */
    private $security;

    public function __construct(ClientRepository $clientRepository, Security $security)
    {
        $this->clientRepository = $clientRepository;
        $this->security = $security;

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client) {
                    return sprintf(' %s', $client->getName());
                },
                'placeholder' => 'Escoger Cliente...',
                'choices' => $this->clientRepository->findByCompany($this->security->getUser()->getCompany()),
                'required'=>false,
                'label'=>'Client'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
