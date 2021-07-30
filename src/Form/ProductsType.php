<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Merchants;
use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
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
            ->add('merchant')
            ->add('category')
            ->add('tax_percentage');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
            'csrf_protection' => true
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
