<?php
namespace Rotis\CourseMakerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('user', new UserType(),array('label' => 'Création de l\'équipe (* = obligatoire)'));

        $builder->add('joueur',new aJoueurType(),array('label' => 'Creation du chef d\'équipe (* = obligatoire)'));
    }

    public function getName()
    {
        return 'registration';
    }
}
