const Carousell = {
    // select display button
    displayButton: document.querySelector('.gallery-button'),
    // select carousel
    gallery: document.querySelector('.gallery'),

    init: function () {
        Carousell.eventsList();
    },

    // add event listener on button click
    eventsList: function () {
        Carousell.displayButton.addEventListener('click', Carousell.handleClick);
    },

    // display or hide the carousel
    handleClick: function () {
        Carousell.gallery.classList.toggle('d-none')
    }
}
document.addEventListener('DOMContentLoaded', Carousell.init);
