<?php

namespace Cosa\Instant\TimelineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('created_at')
            ->add('updated_at')
            ->add('finish_at')
            ->add('status')
            ->add('nb_views')
            ->add('last_view')
            ->add('lang')
            ->add('message_type')
            ->add('user')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cosa\Instant\TimelineBundle\Entity\Instant'
        ));
    }

    public function getName()
    {
        return 'cosa_instant_timelinebundle_instanttype';
    }
}
