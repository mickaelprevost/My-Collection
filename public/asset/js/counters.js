// select values of all counters
let valueDisplayed = document.querySelectorAll(".num");
let interval = 1000;

valueDisplayed.forEach((values) => {
    // define value from start
    let startvalue = 0;
    // define the endvalue for each counter
    let endvalue = parseInt(values.getAttribute("data-val"));
    // define duration for each counter
    let duration = 3;
    // define the value added each time
    let counter = setInterval(function () {
        // update counter value after each adds
        values.textContent = startvalue += 100;
        // stop the counting when the endvalue is reached
        if (startvalue == endvalue) {
            clearInterval(counter);
        }
}, duration);
});