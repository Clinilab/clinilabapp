<?php

namespace App\Form;

use App\Entity\Orden;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrdenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('fecha', TextType::class)
            ->add('emb')
            ->add('servicio')
            ->add('numExterno')
            ->add('cama')
            ->add('diagnostico')
            ->add('notas')
            ->add('medico')
            ->add('paciente')
            ->add('eapb')
            ->add('imporden')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Orden::class,
            'csrf_protection' => false
        ]);
    }
}
