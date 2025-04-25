<?php

namespace App\Form;

use App\Entity\ResultadoEstudio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResultadoEstudioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('estudio') 
            ->add('nombre') 
            ->add('nota') 
            ->add('unidadMedida')
            ->add('tipo')
            ->add('variableMaquina')
            ->add('formula')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ResultadoEstudio::class,
            'csrf_protection' => false
        ]);
    }
}
