<?php
namespace App\Form\FormDynamique;

use App\Entity\FormulaireFormateurField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Unique;

class FormInputType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("label", TextType::class, [
                "label" => "Label :"
            ])
            ->add("placeholder", TextType::class, [
                "label" => "Placeholder :"
            ])
            ->add("type", ChoiceType::class, [
                "placeholder" => "Choisissez le type de l'élement",
                "choices" => [
                    "Text" => "text",
                    "Checkbox" => "checkbox",
                    "Nombre" => "integer",
                    "Date" => "date",
                    "Téléphone" => "phone",
                    "TextArea" => "textarea",
                    "Email" => "email",
                    "Bouton" => "submit",
                    "Liste" => "select",
                    "Fichier" => "file",
                ]
            ])
            ->add("required", CheckboxType::class, [
                "label" => "Est-il obligaoire ?",
                "required" => false,
                ])
            ->add("error", TextType::class, [
                "label" => "Message si mauvais :",
                "required" => true,
                ])
            ->add("id", HiddenType::class, [
                "required" => false,
            ])
            ->add("position", HiddenType::class, [
                "required" => true,
                "attr" => [
                    "class" => "input_fields_position"
                ]
            ])
            ->add("choices", CollectionType::class, [
                "attr" => [
                    "class" => "hidden"
                ],
                "label" => "Choix",
                "entry_type" => FormChoiceType::class,
                'prototype' => true,
                "allow_add" => true,
                "allow_delete" => true,
                "entry_options" => [
                    "label" => false,
                ],
                "prototype_name" => "__choices__",
                "by_reference" => true,
            ]);

        $builder->get('required')
            ->addModelTransformer(new CallbackTransformer(
                function ($booleanFromArray) {
                    return (bool)$booleanFromArray;
                },
                function ($booleanFromString) {
                    // transform the string back to an array
                    return (bool)$booleanFromString;
                }
            ))
        ;

        return $builder;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormulaireFormateurField::class,
        ]);
    }
}
