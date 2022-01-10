<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\News\Create;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

/**
 * Class Form
 * @package App\Model\Admin\UseCase\News\Create
 */
class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', Type\TextType::class)
            ->add('delayedPublication', Type\TextType::class, ['required' => false])
            ->add('text', CKEditorType::class, [
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}