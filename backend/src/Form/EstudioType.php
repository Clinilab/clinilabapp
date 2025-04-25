<?php

namespace App\Form;

use App\Entity\Estudio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstudioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('nombre')
            ->add('codigo')
            ->add('estudio')
            ->add('areaid')
            ->add('abrev')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Estudio::class,
            'csrf_protection' => false
        ]);
    }
}
