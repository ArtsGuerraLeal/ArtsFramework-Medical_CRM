<?php

namespace App\Form;


use App\Entity\Patient;
use App\Entity\Staff;
use App\Entity\Treatment;
use App\Repository\StaffRepository;
use App\Repository\TreatmentRepository;
use App\Service\Client;
use Doctrine\ORM\EntityManagerInterface;
use Google_Service_Calendar;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PatientAppointmentType extends AbstractType
{
    /**
     * @var TreatmentRepository
     */
    private $treatmentRepository;

    private $security;
    /**
     * @var StaffRepository
     */
    private $staffRepository;
    /**
     * @var \Google_Client
     */
    private $client;
    /**
     * @var EntityManagerInterface
     */
    private $em;


    public function __construct(EntityManagerInterface $em, StaffRepository $staffRepository, TreatmentRepository $treatmentRepository,Security $security,Client $googleClient)
    {
        $this->treatmentRepository = $treatmentRepository;
        $this->security = $security;
        $this->staffRepository = $staffRepository;
        $this->client = $googleClient->getClient($security->getUser()->getCompany()->getGoogleJson()[0]);
        $this->em = $em;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        if($user->getCompany()->getGoogleJson() != null) {

            $service = new Google_Service_Calendar($this->client);
            $calendarList = $service->calendarList->listCalendarList();
            $calendarNames = array();
            $calendarId = array();
            $choices = array();

            foreach ($calendarList->getItems() as $calendarListEntry) {
                $calendarNames[] = $calendarListEntry->getSummary();
                $calendarId[] = $calendarListEntry->getId();
                $choices[$calendarListEntry->getSummary()] = $calendarListEntry->getId();
            }

            $builder->add('calendar',ChoiceType::class,[
                'label'=>'Calendario Asignado',
                'choices' => $choices
            ]);
        }

        $builder
            ->add('treatment', EntityType::class, ['label'=>'Tratamiento',
                'class' => Treatment::class,
                'choice_label' => function(Treatment $treatment) {
                    return sprintf(' %s', $treatment->getName());
                },
                'placeholder' => 'Choose an Treatment',
                'choices' => $this->treatmentRepository->findByCompany($user->getCompany()),
                'multiple' => false
            ])

            ->add('notes',TextType::class,['label'=>'Notas',
                'required'=> false])

            ->add('staff', EntityType::class, ['label'=>'Persona Asignada',
                'required'=>false,
                'class' => Staff::class,
                'choice_label' => function(Staff $staff) {
                    return sprintf(' %s', $staff->getFirstName()." ". $staff->getLastName());
                },
                'placeholder' => 'Selecciona la persona asignada',
                'choices' => $this->staffRepository->findByCompany($user->getCompany()),

            ])
        /*
            ->add('cost', MoneyType::class,['label'=>'Costo',
                'required'=>false,
                'currency'=> 'USD'])

            ->add('discount', MoneyType::class,['label'=>'Descuento',
                'required'=>false,
                'currency'=> 'USD'])

            ->add('totalcost', MoneyType::class,['label'=>'Costo Total',
                    'required'=>false,
                    'currency'=> 'USD',
                    'attr'=>['readonly'=>true]
                ]
            )
        */
            ->add('beginAt',DateTimeType::class, [
                'label'=>'Tratamiento',
                'widget'=> 'single_text',
                'html5'=> false,
                'attr'=>['readonly'=>true]


            ])
            ->add('endAt',DateTimeType::class, [
                'label'=>'Tratamiento',
                'widget'=> 'single_text',
                'html5'=> false,
                'attr'=>['readonly'=>true]

            ])
            ->add('color',ChoiceType::class,[
                'label'=>'Color',
                'choices' => [
                    'Red' =>'red',
                    'Orange' =>'orange',
                    'Yellow' =>'yellow',
                    'Green' =>'green',
                    'Blue' =>'blue',
                    'Purple' =>'purple'
                ]

            ])

            ->add('save',SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
  //      $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

    }

    protected function addElements(FormInterface $form, Treatment $treatment = null) {
        // 4. Add the province element
        $form->add('treatment', EntityType::class, array(
            'required' => true,
            'data' => $treatment,
            'placeholder' => 'Select a treatment...',
            'class' => Treatment::class
        ));

        // Neighborhoods empty, unless there is a selected City (Edit View)
        $treatments = array();
        $user = $this->security->getUser();
        // If there is a city stored in the Person entity, load the neighborhoods of it
        if ($treatment) {
            // Fetch Neighborhoods of the City if there's a selected city
            $repoTreatments = $this->em->getRepository(Treatment::class);

            $treatments = $this->treatmentRepository->findByCompany($user->getCompany());
        }

        // Add the Neighborhoods field with the properly data

    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected City and convert it into an Entity
        $city = $this->em->getRepository('AppBundle:City')->find($data['city']);

        $this->addElements($form, $city);
    }

    function onPreSetData(FormEvent $event) {
        $person = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the City is always empty
//        $city = $->getCity() ? $person->getCity() : null;

  //      $this->addElements($form, $city);
    }


}
