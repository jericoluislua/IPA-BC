function removeExtraClasses(classToSearch, classToRemove){
    const allClassToSearchElements = document.getElementsByClassName(classToSearch);
    //Goes through all elements that have the classes 'form-label'
    for (let i = 0; i < allClassToSearchElements.length; i++) {
        //Remove class 'classToRemove' if it coexists with 'classToSearch'
        allClassToSearchElements[i].classList.remove(classToRemove);
    }
}

removeExtraClasses('form-label', 'input-group-text'); /*Remove all input-group-text classes in all elements with class form-label*/
removeExtraClasses('input-group-text', 'required'); /*Remove all required classes in all elements with class input-group-text*/
