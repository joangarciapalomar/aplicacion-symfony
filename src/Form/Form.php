<?php



namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\Autor;

class CancionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class)
            ->add('duracion', NumberType::class)
            ->add('autor', EntityType::class, array(
                'class' => Autor::class,
                'choice_label' => 'nombre'
            ))
            ->add('save', SubmitType::class, array('label' => 'Enviar'));
    }
}
