<?php

namespace App\Form;

use App\Entity\Consecutivo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsecutivoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('nombre')
            ->add('prefijo')
            ->add('consecutivo')
            ->add('fechaactual')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Consecutivo::class,
            'csrf_protection' => false
        ]);
    }
}
