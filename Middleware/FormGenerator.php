<?php
namespace App\Service;

use App\Entity\FormulaireChoices;
use App\Entity\FormulaireFormateur;
use App\Entity\FormulaireFormateurField;
use App\Service\GenerateFormInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Validator;
use Twig\Environment;
use Vich\UploaderBundle\Form\Type\VichFileType;
use function Symfony\Component\String\u;

class FormGenerator
{
    const AuthorizeEntryName = [
        "label_input",
        "type_input",
        "require_input",
        "placeholder_input",
        "position_input"
    ];

    private array $data = [];
    private array $fields = [];
    private array $usesType = [];
    private Environment $twig;
    private array $inputs = [];

    private GenerateFormInterface $form;

    private EntityManagerInterface $entityManager;

    private string $rootDir;

    public function __construct(string $rootDir, Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->rootDir = $rootDir;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function generateForm(GenerateFormInterface &$form): self
    {
        $this->form = $form;

        /** @var FormulaireFormateurField[] $formFields */
        $formFields = $form->getFields()->getValues();

        uasort($formFields,  function ($a, $b) {
            return $a->getPosition() > $b->getPosition();
        });

        /**
         * @var int $key
         * @var FormulaireFormateurField $field
         */
        foreach ($formFields as $key => $field) {
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($field->getLabel())->snake()->toString();

            $field->setSlug($slug);
            $field->setFormulaireFormateur($form);

            $this->fields[$slug] = [
                "parameters" => [
                    "label" => $field->getLabel(),
                    "attr" => [
                        "placeholder" => $field->getPlaceholder(),
                        "id" => $slug,
                    ]
                ],
                "type" => $this->translateType($field->getType()),
            ];

            if($field->getType() !== "submit"){
                $this->fields[$slug]["parameters"]["required"] = $field->getRequired();
                $this->fields[$slug]["parameters"]["invalid_message"] = $field->getError();
            }

            if($field->getType() === "file"){
                $this->fields[$slug]["parameters"]["help"] = "Fichier .pdf de taille maximal de 10Mo";
                $this->fields[$slug]["parameters"]["attr"]["placeholder"] = "";
            }

            if($field->getType() === "select"){
                $this->fields[$slug]["parameters"]["placeholder"] = $field->getPlaceholder();
            }

            if($field->getType() === "date"){
                $this->fields[$slug]["parameters"]["widget"] ='single_text';
            }

            if(!$field->getChoices()->isEmpty()){
                $this->fields[$slug]["parameters"] = array_merge($this->fields[$slug]["parameters"], ["choices" => $this->translateChoices($field->getChoices()->getValues())]);
            }
        }

        return $this;
    }

    public function createFormTypeClass(string $className)
    {

        $this->generateFile([
            "form_fields" => $this->fields,
            "usesType" => array_unique($this->usesType),
            "class_name" => u($className)->camel()->title()->toString() . "Form",
            "prefix" => u($className)->snake()
        ]);
    }

    public function getInputs()
    {
        return $this->inputs;
    }

    private function translateType(string $type): string
    {
        switch ($type) {
            case "text":
                $this->usesType[] = TextType::class;
                $typeString = "TextType::class";
                break;
            case "checkbox":
                $this->usesType[] = CheckboxType::class;
                $typeString = "CheckboxType::class";
                break;
            case "integer":
                $this->usesType[] = IntegerType::class;
                $typeString = "IntegerType::class";
                break;
            case "date":
                $this->usesType[] = DateType::class;
                $typeString = "DateType::class";
                break;
            case "phone":
                $this->usesType[] = TelType::class;
                $typeString = "TelType::class";
                break;
            case "address":
                $this->usesType[] = TextType::class;
                $typeString = "AddressType::class";
                break;
//            case "captcha":
//                $this->usesType[] = CaptchaType::class;
//                $typeString =  "CaptchaType::class";
//                break;
            case "textarea":
                $this->usesType[] = TextAreaType::class;
                $typeString = "TextAreaType::class";
                break;
            case "email":
                $this->usesType[] = EmailType::class;
                $typeString = "EmailType::class";
                break;
            case "submit":
                $this->usesType[] = SubmitType::class;
                $typeString = "SubmitType::class";
                break;
            case "select":
                $this->usesType[] = ChoiceType::class;
                $typeString = "ChoiceType::class";
                break;
            case "file":
                $this->usesType[] = VichFileType::class;
                $typeString = "VichFileType::class";
                break;
            default:
                $typeString = "TextType::class";
        }

        return $typeString;
    }

    private function generateFile(array $data)
    {
        ob_start();

        extract($data);
        require $this->rootDir . "/templates/Admin/form/FormType.tpl.php";
        $content = ob_get_clean();
        $file = $this->rootDir . "/src/Form/Frontend/" . $data["class_name"] . ".php";
        file_put_contents($file, $content);

        $this->form->setPath($file);
    }

    private function translateChoices(array $choicesString = []): array
    {
        if (empty($choicesString)) {
            return [];
        }

        $choices = [];

        /** @var FormulaireChoices $choice */
        foreach ($choicesString as $choice) {
            $choices[$choice->getLabel()] = $choice->getValue();
        }

        return $choices;
    }
}
