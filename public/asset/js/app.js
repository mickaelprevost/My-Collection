 const bioEdit = {
    // select form 
    formBioEdit: document.querySelector(".js-formBioEdit"),
    // select textarea 
    inputBio: document.querySelector(".js-inputBio"),
    // select the content of the text area 
    valueInputBio: document.querySelector(".js-inputBio").value,
    // select the modify button 
    buttonEdit: document.querySelector(".js-bioEdit"),
    // select the submit button of the form 
    buttonSubmit: document.querySelector(".js-bioSubmit"),

    // set the initials conditions 
    init: function()
    {
        bioEdit.attachEvents();
        bioEdit.buttonSubmit.classList.add("d-none");
    },

    // we add the event listeners on the bio modify button and the submit button of the form 
    attachEvents: function()
    {
        bioEdit.buttonEdit.addEventListener("click", bioEdit.displayButtonSubmit)
        bioEdit.formBioEdit.addEventListener("submit", bioEdit.submitForm)
    },

    /* on modify click we remove the disabled attribute to be able to write the text in the textarea 
    then we hide the modify button to show the submit button instead */
    displayButtonSubmit: function()
    {
        bioEdit.inputBio.removeAttribute("disabled");
        bioEdit.buttonEdit.classList.add("d-none");
        bioEdit.buttonSubmit.classList.remove("d-none");
    },

    /* on submit click we prevent the page from reloading then we hide the submit button 
    to get the modify button back */
    submitForm: async function(e)
    {
        e.preventDefault();

        const response = await fetch(window.location.href, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                summary: document.querySelector(".js-inputBio").value,
                token: document.querySelector(".js-bioToken").value,
            })
        });

        const data = await response.json();

        if (data.code === 200) {
            bioEdit.inputBio.textContent = document.querySelector(".js-inputBio").value;
            bioEdit.inputBio.setAttribute("disabled", "disabled");
            bioEdit.buttonEdit.classList.remove("d-none");
            bioEdit.buttonSubmit.classList.add("d-none");
        }
    }
}

document.addEventListener("DOMContentLoaded", bioEdit.init);