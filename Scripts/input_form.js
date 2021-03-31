import './commun_fonction';
import {resetPosition} from "./commun_fonction";
import Sortable from "sortablejs";

(function () {
    const button = document.getElementsByClassName("add-new-element")

    button.forEach((ele) => {
        ele.addEventListener("click", () => addInput(ele))
    })

    const selects = document.getElementsByTagName("select")

    selects.forEach((ele) => {
        ele.addEventListener("change", () => handleSelectChange(ele))
        handleSelectChange(ele)
    })

    const delButtons = document.querySelectorAll(".remove-fields-input")

    delButtons.forEach((ele) => {
        ele.addEventListener("click", () => removeElement(ele))
    })

    const choiceHide = document.querySelectorAll(".hide-choice-list")

    choiceHide.forEach((ele) => {
        ele.addEventListener("click", () => hideChoices(ele))
    })

    let el = document.getElementById('input-fields-list');
    let choiceList = el.querySelectorAll('ul.input-list-choices');

    choiceList.forEach((ele) => {
        let elChoice = document.getElementById(ele.id);
        new Sortable(elChoice, {
            animation: 150,
            ghostClass: 'blue-background-class',
            swap: true, // Enable swap plugin
            swapClass: 'highlight', // The class applied to the hovered swap item
            // Element dragging ended
            onEnd: function (evt) {
                resetPosition(ele.id)
            },
        });
    })

    new Sortable(el, {
        animation: 150,
        ghostClass: 'blue-background-class',
        swap: true, // Enable swap plugin
        swapClass: 'highlight', // The class applied to the hovered swap item
        // Element dragging ended
        onEnd: function (evt) {
            resetPosition('input-fields-list')
        },
    });
})();

function removeElement(button) {
    let target = button.parentNode
    let targetParent = target.parentNode
    let targetId = targetParent.id
    targetParent.removeChild(target)
    resetPosition(targetId)
}

function hideChoices(button) {
    let parent = button.parentNode

    const list = parent.querySelectorAll("li")
    let status = button.querySelectorAll(".hide-choice-list-status")
    status.forEach((ele) => ele.classList.toggle("hidden"))
    list.forEach((ele) => {
        if (button.dataset.display === "hide") {
            ele.classList.remove("hidden")
        } else{
            ele.classList.add("hidden")
        }
    })

    button.dataset.display = button.dataset.display === "show" ? 'hide' : "show"
}

function addInput(element) {
    const targetList = element.dataset.listSelector
    const list = document.getElementById(targetList)
    let counter = parseInt(list.dataset.widgetCounter) || list.children.length
    let key = new RegExp("__" + element.dataset.key + "__", 'g')

    let newWidget = list.dataset.prototype

    console.log(element)
    newWidget = newWidget.replace(key, counter)

    list.dataset.widgetCounter = counter + 1

    const dom = document.createElement(list.dataset.widgetTags ?? "li");
    dom.innerHTML = newWidget;
    dom.setAttribute("draggable", true)

    const choiceButton = dom.querySelector(".add-new-element")

    let select = dom.querySelector(`#form_fields_${counter}_type`)
    let choices = dom.querySelector(`#form_fields_${counter}_choices`)

    if (choices) {
        choices.parentNode.classList.add("hidden")
    }

    if (select) {
        select.addEventListener("change", () => handleSelectChange(select, counter, choices.parentNode, choiceButton))
    }

    let delButton = dom.querySelector(".remove-fields-input")

    if (!delButton) {
        delButton = document.createElement("button")
        delButton.innerHTML = "Supprimer"
        delButton.classList.add("w-1/5" ,"mt-5" ,"remove-fields-input" ,"bg-red-700" ,"text-white" ,"font-bold" ,"py-2" ,"px-4" ,"rounded" ,"inline-flex" ,"items-center")
        dom.appendChild(delButton)
    }
    delButton.addEventListener("click", () => removeElement(delButton))

    if (choiceButton) {
        choiceButton.addEventListener("click", () => addInput(choiceButton))
    }

    list.appendChild(dom)

    let positionInput = dom.querySelector(`#form_fields_${counter}_position`)

    if (!positionInput) {
        positionInput = dom.querySelector(`#${targetList}_${counter}_position`)
    }

    const choiceHide = document.querySelector(".hide-choice-list")
    if(choiceHide) {
        choiceHide.addEventListener("click", () => hideChoices(choiceHide))
    }

    positionInput.value = counter + 1
}

function handleSelectChange(select, counter = null, choices = null, choiceButton = null) {

    if (choices == null) {
        let container = select.parentNode.parentElement
        counter = container.dataset.counter
        choices = container.querySelector(`#form_fields_${counter}_choices`)
        choiceButton = container.querySelector(".add-new-element")
    }

    const elRequire = document.getElementById(`form_fields_${counter}_required`);

    if (select.value === "select") {
        choices.classList.remove("hidden")
        choiceButton.classList.remove("hidden")

        const elChoice = document.getElementById(`form_fields_${counter}_choices`);
        new Sortable(elChoice, {
            animation: 150,
            ghostClass: 'blue-background-class',
            swap: true, // Enable swap plugin,
            swapClass: 'highlight', // The class applied to the hovered swap item
            // Element dragging ended
            onEnd: function (evt) {
                resetPosition(`form_fields_${counter}_choices`)
            },
        });
    } else if (select.value === "submit") {
        elRequire.parentNode.parentNode.classList.add("hidden")
        choices.classList.add("hidden")
        choiceButton.classList.add("hidden")
    } else {
        choices.classList.add("hidden")
        choiceButton.classList.add("hidden")
        elRequire.parentNode.parentNode.classList.remove("hidden")
    }
}