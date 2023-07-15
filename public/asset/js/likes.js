//We are using axios library to handle ajax requests src="https://unpkg.com/axios/dist/axios.min.js">

function onClickBtnLike(event) {
    // we prevent form redirecting to url with json info "like added or removed ..."
    event.preventDefault();

    // we want the url of the event to call it with ajax   here "this"= the event element "the whole <a> tag"
    const url = this.href;
    // select the element in the <a> indicating the number of likes 
    const spanCount = this.querySelector('span.js-likes');
    // select like icone
    const icone = this.querySelector('svg');

    // we get the url of the event containing a "promise" then we work with the response in a function
    axios.get(url).then(function(response) {
        // we define the value of the element indicating the amount of likes at the number of likes in the response + string like(s)
        spanCount.textContent = response.data.likes + ' like(s)';

    // we handle the icone of the like depending if the element is liked or not
    if (icone.classList.contains('bi-hand-thumbs-up-fill')) {
        icone.classList.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up');
    } else {
        icone.classList.replace('bi-hand-thumbs-up', 'bi-hand-thumbs-up-fill');
    }
    // if we have an error with the response
    }).catch(function(error) {
    if (error.response.status === 403) {
        window.alert("vous ne pouvez pas liker un commentaire sans être identifié!")
    } else {
        window.alert("Une erreur s'est produite veuillez réessayer plus tard.")
    }
    });
}
document.querySelectorAll('a.js-like').forEach(function(link) {
    link.addEventListener('click', onClickBtnLike);
})