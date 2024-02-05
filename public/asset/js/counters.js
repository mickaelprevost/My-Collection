// select values of all counters
let valueDisplayed = document.querySelectorAll(".num");

valueDisplayed.forEach((values) => {
    // define value from start
    let startvalue = 0;
    // define the endvalue for each counter
    let endvalue = parseInt(values.getAttribute("data-val"));
    // define duration for each counter
    let duration = 60;
    // define the value added each time
    let counter = setInterval(function () {
        // update counter value after each adds
        values.textContent = startvalue += 1;
        // stop the counting when the endvalue is reached
        if (startvalue == endvalue) {
            clearInterval(counter);
        }
}, duration);
});