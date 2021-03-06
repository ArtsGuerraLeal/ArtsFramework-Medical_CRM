<?php

namespace App\Form;

use App\Entity\Vendor;
use App\Entity\Product;
use App\Entity\Category;
use App\Repository\VendorRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProductType extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var VendorRepository
     */
    private $vendorRepository;
    /**
     * @var Security
     */
    private $security;
    public function __construct(VendorRepository $vendorRepository, CategoryRepository $categoryRepository, Security $security)
    {
        $this->categoryRepository = $categoryRepository;
        $this->vendorRepository = $vendorRepository;
        $this->security = $security;

    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Nombre'

            ])
            ->add('sku', TextType::class,[
                'required'=>false,
                'label'=>'SKU'

            ])
            ->add('upc', TextType::class,[
                'required'=>false,
                'label'=>'UPC'

            ])
            ->add('price',NumberType::class,[
                'required' => false,
                'label'=>'Precio'

            ])
            ->add('cost',NumberType::class,[
                'required' => false,
                'label'=>'Costo'

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
                'choices' => $this->categoryRepository->findByCompany($this->security->getUser()->getCompany()),
                'required'=>false,
                'label'=>'Categoria'
            ])
            ->add('vendor',EntityType::class, [
                'class' => Vendor::class,
                'choice_label' => function(Vendor $vendor) {
                    return sprintf(' %s', $vendor->getName());
                },
                'placeholder' => 'Escoger Proveedor ...',
                'choices' => $this->vendorRepository->findByCompany($this->security->getUser()->getCompany()),
                'required'=>false,
                'label'=>'Proveedor'
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
