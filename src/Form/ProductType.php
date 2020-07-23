<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProductType extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var Security
     */
    private $security;
    public function __construct(CategoryRepository $categoryRepository, Security $security)
    {
        $this->categoryRepository = $categoryRepository;
        $this->security = $security;

    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Nombre'

            ])
            ->add('price',NumberType::class,[
                'required' => false,
                'label'=>'Precio'

            ])
            ->add('isTaxable',CheckboxType::class,[
                'required' => false,
                'label'=>'Incluye IVA?'
            ])
            ->add('category',EntityType::class, [
                'class' => Category::class,
                'choice_label' => function(Category $category) {
                    return sprintf(' %s', $category->getName());
                },
                'placeholder' => 'Escoger categoria ...',
                'choices' => $this->categoryRepository->findAll(),
                'required'=>false,
                'label'=>'Categoria'
            ])
            ->add('quantity',NumberType::class,[
                'required' => false,
                'label'=>'Cantidad'
            ])
            ->add('attachment',FileType::class , [
                'mapped' => false,
                'required' => false,
                'label'=>'Foto'

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
