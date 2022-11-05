<?php

namespace App\Form;

use App\Entity\Plant;
use App\Entity\Seed;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PlantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('qty')
            ->add('date_updated', DateType::class, [
                'data' => new \DateTime(),
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Germination'   => 'Germination',
                    'Croissance'    => 'Croissance',
                    'Floraison'     => 'Floraison',
                    'Recolte'       => 'Recolte'
                ]
            ])
            ->add('seed', EntityType::class,[
                'class' => Seed::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
