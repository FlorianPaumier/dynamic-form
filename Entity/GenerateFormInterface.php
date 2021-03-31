<?php


namespace App\Service;


interface GenerateFormInterface
{

    public function getFields();
    public function addField(GenerateFormInputInterface $input);
    public function setFields(array $fields = null);
}
