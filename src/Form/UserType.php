<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Establecimiento;
use App\Entity\Mesa;
use App\Entity\Localidad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Doctrine\ORM\EntityRepository;

class UserType extends AbstractType
{
    private $em;

    /**
     * The Type requires the EntityManager as argument in the constructor. It is autowired
     * in Symfony 3.
     * 
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [
            'Niguno' => '[]',
            'Usuario' => '["ROLE_USER"]',
            'Administrador' => '["ROLE_ADMIN"]',
        ];

        $builder
            ->add('nombre', null, ['label' => 'Nombre'])
            ->add('apellido', null, ['label' => 'Apellido'])
            //->add('password', PasswordType::class, ['label' => 'Contraseña'])
            ->add('email', EmailType::class, ['label' => 'E-mail'])
            ->add('rolesString', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => $choices,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
