/**
 * Permet de mettre à jours la position des éléments dynamiquement
 * @param String targetId | id de l'élément à update
 */
export function resetPosition(targetId){
    const target = document.getElementById(targetId);
    let result = target.querySelectorAll(".input_fields_position")
    let position = document.querySelectorAll(".input-position-title")

    result.forEach((input, key) => input.value = key + 1)
    position.forEach((input, key) => input.innerHTML = key + 1)
}
