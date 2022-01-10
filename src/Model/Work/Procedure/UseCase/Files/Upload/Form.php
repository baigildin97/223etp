<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Files\Upload;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('fileTitle', Type\TextType::class)
            ->add('file', FileType::class, ['constraints' => [
            new \Symfony\Component\Validator\Constraints\File([
                'maxSize' => '10000k',
                'mimeTypes' => [
                    'application/pdf',
                    'application/x-pdf',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/msword',
                    'image/jpeg'

                ],
                'mimeTypesMessage' => 'Неверный формат файла для загрузки. Допустимые форматы к загрузке: pdf, doc, docx, jpg, jpeg',
            ])
        ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}