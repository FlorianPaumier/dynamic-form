<?php

namespace App\Form\FormDynamique;

use App\Form\ImageType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder
            ->add("name", TextType::class, [
                "label" => "Nom :",
                "required" => true
            ])
            ->add("title", TextType::class, [
                "label" => "Titre :",
                "required" => true
            ])
            ->add("description", CKEditorType::class, [
                "label" => "Description :",
                "required" => true
            ])
            ->add("image", ImageType::class, [
                "label" => "Image :",
                "required" => false
            ])
            ->add("enable", CheckboxType::class, [
                "label" => "Activer ?",
                "required" => false
            ])
            ->add("fields", CollectionType::class, [
                "label" => "Champs:",
                "entry_type" => FormInputType::class,
                'prototype' => true,
                "allow_add" => true,
                "allow_delete" => true,
                "entry_options" => [
                    "label" => false,
                ],
                "required" => true,
                "prototype_name" => "__fields__"
            ])
            ;
    }
}
