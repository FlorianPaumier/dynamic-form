<?php


namespace App\Service;



use App\Entity\FormulaireChoices;
use Doctrine\Common\Collections\Collection;

interface GenerateFormInputInterface
{

    /**
     * @return string
     */
    function getSlug(): ?string;

    /**
     * @param string $formKey
     * @return GenerateFormInputInterface
     */
    function setSlug(string $formKey): self;

    /**
     * @return string
     */
    function getLabel(): ?string;

    /**
     * @param string $label
     * @return GenerateFormInputInterface
     */
    function setLabel(string $label): self;

    /**
     * @return string
     */
    function getType(): ?string;

    /**
     * @param string $type
     * @return self;
     */
    function setType(string $type): self;


    /**
     * @return string
     */
    function getRequired(): ?bool;

    /**
     * @param string $required
     * @return self;
     */
    function setRequired(bool $required): self;

    /**
     * @return string
     */
    function getPlaceholder(): ?string;

    /**
     * @param string $placeholder
     * @return self;
     */
    function setPlaceholder(string $placeholder): self;

    /**
     * @return FormulaireChoices|null
     */
    function getChoices(): ?Collection;

    /**
     * @param FormulaireChoices|null $choice
     * @return self;
     */
    function addChoice(FormulaireChoices $choice): self;

    /**
     * @param FormulaireChoices|null $choice
     * @return self;
     */
    function removeChoice(FormulaireChoices $choice): self;

    /**
     * @return int
     */
    function getPosition(): ?int;

    /**
     * @param int $position
     * @return self;
     */
    function setPosition(int $position): self;

}
