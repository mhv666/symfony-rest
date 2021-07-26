<?php

namespace App\Form\Type;

use App\Form\Model\ProductsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class)
            ->add('image', UrlType::class)
            ->add('description', TextareaType::class)
            ->add('merchant', MerchantsFormType::class)
            ->add('category', CategoriesFormType::class)
            ->add('price', NumberType::class)
            ->add('ean13', NumberType::class)
            ->add('stock', NumberType::class)
            ->add('tax_percentage', NumberType::class)
            ->add('created_at', DateType::class, ['widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'html5' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false
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
