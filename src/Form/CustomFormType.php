<?php

namespace App\Form;

use App\Entity\CustomForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,['label' => false])
            ->add('customdata',CollectionType::class,[
                'label' => false,
                'entry_type'=>CustomDataType::class,
                'entry_options'=>[
                    'label' => false
                ],
                'by_reference' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'mapped' => false,

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
            'data_class' => CustomForm::class,
        ]);
    }
}
