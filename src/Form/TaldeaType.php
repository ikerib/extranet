<?php

namespace App\Form;

use App\Entity\Taldea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaldeaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \App\Controller\SecurityController $ld */
        $ld = $options['ldap'];

        $sarbideak = $ld->getLdapInfo( 'Sarbide*' );

        $zerrenda = [];

        /** @var \Symfony\Component\Ldap\Entry $s */
        foreach ( $sarbideak as $s) {

            $izena = $s->getAttribute( 'name' )[0];

            if ( ! array_key_exists( $izena, $zerrenda)) {
                $zerrenda[$izena]=$izena;
            }
        }

        asort( $zerrenda );

        $builder
            ->add('name', ChoiceType::class, array(
                'choices'   => $zerrenda,
                'multiple'  =>false,
                'placeholder' => 'Aukeratu bat...',
            ))
            ->add('enabled')
//            ->add('karpetak')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Taldea::class,
        ]);
        $resolver->setRequired('ldap');
    }
}
