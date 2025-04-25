<?php

namespace App\Form;

use App\Entity\ValorResultado;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValorResultadoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('valor')
            ->add('resultadoEstudio')
            ->add('ordenEstudio')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ValorResultado::class,
            'csrf_protection' => false
        ]);
    }
}
