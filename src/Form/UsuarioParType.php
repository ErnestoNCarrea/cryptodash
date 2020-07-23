<?php

namespace App\Form;

use App\Entity\UsuarioPar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioParType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('divisa1', null, [
                'label' => 'Divisa 1',
                'required' => true,
            ])
            ->add('divisa2', null, [
                'label' => 'Divisa 2',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsuarioPar::class,
        ]);
    }
}
