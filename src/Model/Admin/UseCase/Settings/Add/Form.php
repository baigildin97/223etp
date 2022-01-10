<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Settings\Add;


use App\Model\Admin\Entity\Settings\Key;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

/**
 * Class Form
 * @package App\Model\Admin\UseCase\Settings\Add
 */
class Form extends AbstractType
{
    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    /**
     * Form constructor.
     * @param SettingsFetcher $settingsFetcher
     */
    public function __construct(SettingsFetcher $settingsFetcher) {
        $this->settingsFetcher = $settingsFetcher;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = Key::$keys;
        if ($array = $this->settingsFetcher->allArrayKeyList()){
            foreach ($array as $list){
                $key = array_keys(array_flip($list));
                unset($choices[current($key)]);
            }

        }

        $builder
            ->add('key', Type\ChoiceType::class,[
                'label' => 'Статус',
                'choices' => array_flip($choices)
            ])
            ->add('value', Type\TextType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class
        ]);
    }

}