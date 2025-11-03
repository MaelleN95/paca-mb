<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

    $builder
        ->add('imageFile', VichImageType::class, [
            'label' => 'Image (JPG ou PNG)',
            'required' => false,
            'download_uri' => false,
            'allow_delete' => true,
            'image_uri' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductImage::class,
        ]);
    }
}
