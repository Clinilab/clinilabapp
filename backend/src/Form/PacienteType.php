<?php

namespace App\Form;

use App\Entity\Paciente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PacienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('fechaNacimiento', TextType::class)
            ->add('direccion')
            ->add('notas')
            ->add('foto')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Paciente::class,
            'csrf_protection' => false
        ]);
    }
}
