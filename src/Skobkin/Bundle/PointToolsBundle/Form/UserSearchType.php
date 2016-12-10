<?php

namespace Skobkin\Bundle\PointToolsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Skobkin\Bundle\PointToolsBundle\Entity\User',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'skobkin_bundle_pointtoolsbundle_user_search';
    }
}
