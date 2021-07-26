<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Merchants;
use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image')
            ->add('color')
            ->add('price')
            ->add('ean13')
            ->add('stock')
            ->add('merchant', MerchantsType::class, [
                'data_class' => Merchants::class,
            ])
            ->add('category', CategoriesType::class, [
                'data_class' => Categories::class,
            ])
            ->add('tax_percentage');
        /*
            ->add('merchant', CollectionType::class, array(
                'entry_type' => MerchantsType::class,
                'allow_add' => true, //This should do the trick. 
            ))
            ->add('category', CollectionType::class, array(
                'entry_type' => CategoriesType::class,
                'allow_add' => true, //This should do the trick. 
            ));*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }

    public function getName()
    {
        return '';
    }
}
