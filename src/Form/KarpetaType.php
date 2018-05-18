<?php

namespace App\Form;

use App\Entity\Karpeta;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KarpetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $finder = new Finder();
        $dirs = $finder->directories()->in( getenv( 'APP_FOLDER_PATH' ) )->depth('<1')->sortByName();

        $karpetak = [];

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $em = $options['entity_manager'];

        /** @var \DirectoryIterator $d */
        foreach ($dirs as $d) {
            if ( $em->getRepository('App:Karpeta')->isThisFolderOnMysql($d->getRealPath()) == null ) {
                $karpetak[$d->getBasename()] = $d->getRealPath();
            }
        }


        $builder
            ->add('name', TextType::class, array(
                'empty_data' => 'Idatzi izen bat ...'
            ))
            ->add('path', ChoiceType::class, array(
                'choices'   => $karpetak,
                'multiple'  =>false,
                'placeholder' => 'Aukeratu bat...',
            ))
            ->add('enabled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Karpeta::class,
        ]);
        $resolver->setRequired('entity_manager');
    }
}
