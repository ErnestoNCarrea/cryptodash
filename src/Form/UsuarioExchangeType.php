<?php

namespace App\Form;

use App\Entity\UsuarioExchange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioExchangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('exchange', null, [
                'label' => 'Exchange',
                'required' => true,
            ])
            ->add('cuenta', null, [
                'label' => 'Cuenta',
                'required' => false,
            ])
            ->add('clave', null, [
                'label' => 'Calve',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsuarioExchange::class,
        ]);
    }
}
