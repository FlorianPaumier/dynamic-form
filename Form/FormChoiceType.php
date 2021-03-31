<?php

namespace App\Form\FormDynamique;

use App\Entity\FormulaireChoices;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormChoiceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder
            ->add("label", TextType::class, [
                "label" => "Label :",
                "required" => true
            ])
            ->add("value", TextType::class, [
                "label" => "Valeur :",
                "required" => true
            ])
            ->add("id", HiddenType::class, [
                "required" => false
            ])
            ->add("position", HiddenType::class, [
                "required" => false,
                "attr" => [
                    "class" => "input_fields_position"
                ]
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormulaireChoices::class,
        ]);
    }
}
