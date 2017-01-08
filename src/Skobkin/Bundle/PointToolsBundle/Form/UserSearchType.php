<?php

namespace Skobkin\Bundle\PointToolsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @return string
     */
    public function getName()
    {
        return 'skobkin_bundle_pointtoolsbundle_user_search';
    }
}
