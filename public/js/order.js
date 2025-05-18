"use strict";

document.addEventListener("DOMContentLoaded", (event) => {
    // Capture the initially checked radio button value on page load
    const initialRadio = document.querySelector(
        'input[name="academic_level"]:checked'
    );
    // get topic innitially
    let level = document.getElementById("level");
    if (initialRadio) {
        // console.log("Initially selected academic level: " + initialRadio.value);
        level.innerHTML = "Academic Level : " + initialRadio.value;
    }

    let topic = document.getElementById("topic").value;
    document.getElementById("topc").innerHTML = "Topic : " + topic;

    // deadline
    const deadline = document.querySelector('input[name="deadline"]:checked');
    if (deadline) {
        // var ded = deadline.value;
        document.getElementById("d-line").innerHTML =
            "Deadline : " + deadline.value;
        // console.log(deadline.value);
    }

    // calPrice
    calPrice();

    const format = document.querySelector('input[name="PaperFormat"]:checked');
    let pformat = document.getElementById("p-format");
    if (format) {
        // console.log("Initially selected academic level: " + initialRadio.value);
        pformat.innerHTML = "Paper Format : " + format.value;
    }
});

function increaseValue() {
    var value = parseInt(document.getElementById("number").value, 10);

    value = isNaN(value) ? 0 : value;
    value++;
    document.getElementById("number").value = value;

    document.getElementById("words").innerHTML = calWords(value);
    document.getElementById("pg").innerHTML = value;

    calPrice();
}

function decreaseValue() {
    var value = parseInt(document.getElementById("number").value, 10);
    value = isNaN(value) ? 0 : value;
    value < 1 ? (value = 1) : "";
    value--;
    document.getElementById("number").value = value;

    document.getElementById("words").innerHTML = calWords(value);
    document.getElementById("pg").innerHTML = value;

    calPrice();
}

function decreaseV() {
    // homepage

    var valu = parseInt(document.getElementById("page").value, 10);

    valu < 1 ? (valu = 1) : "";
    valu--;
    document.getElementById("page").value = valu;

    document.getElementById("word").innerHTML = calWords(valu);
    // document.getElementById("pg").innerHTML = value;
    calPrice();
}
function increaseV() {
    // homepage

    // homepage
    var valu = parseInt(document.getElementById("page").value, 10);
    valu = isNaN(valu) ? 0 : valu;
    valu++;
    document.getElementById("page").value = valu;

    document.getElementById("word").innerHTML = calWords(valu);
    // document.getElementById("pg").innerHTML = value;
    calPrice();
}

function calWords(pages) {
    return pages * 275;
}

function handleRadioChange() {
    // Get the selected radio button's value
    const selectedRadio = document.querySelector(
        'input[name="academic_level"]:checked'
    );

    if (selectedRadio) {
        level.innerHTML = "Academic Level : " + selectedRadio.value;
        // console.log("Selected academic level: " + selectedRadio.value);
        return selectedRadio.value;
    }
}

function handlePaperChange() {
    let paper = document.getElementById("typeofpaper").value;
    // console.log(paper);
    return (document.getElementById("paper").innerHTML =
        "Paper Type: " + paper);
}

function handleDisciplineChange() {
    let discipline = document.getElementById("Discipline").value;
    // console.log(paper);
    return (document.getElementById("discipline").innerHTML =
        "Discipline: " + discipline);
}

function handleTopicChange() {
    let topic = document.getElementById("topic").value;
    document.getElementById("topc").innerHTML = "Topic : " + topic;
}

function increaseVal() {
    let value = parseInt(document.getElementById("num").value, 10);
    value = isNaN(value) ? 0 : value;
    value++;
    document.getElementById("num").value = value;
}

function decreaseVal() {
    let value = parseInt(document.getElementById("num").value, 10);
    value = isNaN(value) ? 0 : value;
    value < 1 ? (value = 1) : "";
    value--;
    document.getElementById("num").value = value;
}
function increaseCharts() {
    let value = parseInt(document.getElementById("charts").value, 10);
    value = isNaN(value) ? 0 : value;
    value++;

    document.getElementById("charts").value = value;
    getChartsValue();
}

function decreaseCharts() {
    let value = parseInt(document.getElementById("charts").value, 10);
    value = isNaN(value) ? 0 : value;
    value < 1 ? (value = 1) : "";
    value--;

    document.getElementById("charts").value = value;
    getChartsValue();
}

function calChartPrice(value) {
    // amount per chart
    let amount = 4.5;
    if (value > 0) {
        amount = value * amount;
        amount = amount.toFixed(2);
        return amount;
    }
}

function increaseSlides() {
    let value = parseInt(document.getElementById("slides").value, 10);
    value = isNaN(value) ? 0 : value;
    value++;

    document.getElementById("slides").value = value;

    calPrice();
}

function decreaseSlides() {
    let value = parseInt(document.getElementById("slides").value, 10);
    value = isNaN(value) ? 0 : value;
    value < 1 ? (value = 1) : "";
    value--;

    document.getElementById("slides").value = value;
    calPrice();
}

function page() {
    let pages = document.getElementById("number").value;
    return pages;
}

function handleDeadlineChange() {
    const deadline = document.querySelector('input[name="deadline"]:checked');
    if (deadline) {
        document.getElementById("d-line").innerHTML =
            "Deadline : " + deadline.value;
        return deadline.value;
    }
}

function checkLevel(level) {
    let amount = 0;
    if (level == "High School") {
        amount = 15;
    } else if (level == "Undergrad.(Yr1-2)") {
        amount = 20;
    } else if (level == "Undergrad.(Yr3-4)") {
        amount = 24;
    } else if (level == "Masters") {
        amount = 26;
    } else if (level == "PhD") {
        amount = 30;
    }

    return amount;
}

function checkDeadline(deadline, amount) {
    let price = 0;

    if (deadline == "8hours") {
        price = 1.2 * amount;
    } else if (deadline == "12hours") {
        price = 1.1 * amount;
    } else if (deadline == "24hours") {
        price = amount;
    } else if (deadline == "48hours") {
        price = 0.98 * amount;
    } else if (deadline == "3days") {
        price = 0.96 * amount;
    } else if (deadline == "5days") {
        price = 0.95 * amount;
    } else if (deadline == "7days") {
        price = 0.94 * amount;
    } else if (deadline == "10days") {
        price = 0.93 * amount;
    } else if (deadline == "14days") {
        price = 0.92 * amount;
    }

    return price;
}

function paperFormat() {
    const format = document.querySelector('input[name="PaperFormat"]:checked');
    let pformat = document.getElementById("p-format");
    if (format) {
        // console.log("Initially selected academic level: " + initialRadio.value);
        pformat.innerHTML = "Paper Format : " + format.value;
    }
}

function checkSpacing() {
    const Spacing = document.querySelector('input[name="Spaces"]:checked');

    return Spacing.value;
}

function checkCategory(price) {
    const category = document.querySelector('input[name="category"]:checked');
    let amt = 0;
    if (category.value == "Best Available") {
        return amt;
    } else if (category.value == "Advanced") {
        return 0.2 * price;
    } else if (category.value == "ENL") {
        return 0.3 * price;
    }
}

function getSlideValue() {
    let value = parseInt(document.getElementById("slides").value, 10);
    value = isNaN(value) ? 0 : value;
    let price = 0;
    let amount = 4.5;
    if (value > 0) {
        price = value * amount;

        document.getElementById("slids").innerHTML =
            " x $ " + amount.toFixed(2);
        document.getElementById("s-total").innerHTML = "$" + price.toFixed(2);
        if (value == 1) {
            document.getElementById("sl").innerHTML = value + " Slide";
        } else {
            document.getElementById("sl").innerHTML = value + " Slides";
        }

        return price;
    } else {
        document.getElementById("sl").innerHTML = "";
        document.getElementById("slids").innerHTML = "";
        document.getElementById("s-total").innerHTML = "";
        return price;
    }
}

function getChartsValue() {
    let value = parseInt(document.getElementById("charts").value, 10);
    value = isNaN(value) ? 0 : value;
    let price = 0;
    let amount = 4.5;
    // =================================
    if (value > 0) {
        price = value * amount;

        document.getElementById("chats").innerHTML =
            " x $ " + amount.toFixed(2);
        document.getElementById("c-total").innerHTML = "$" + price.toFixed(2);
        if (value == 1) {
            document.getElementById("ch").innerHTML = value + " Chart";
        } else {
            document.getElementById("ch").innerHTML = value + " Charts";
        }
        return price;
    } else {
        document.getElementById("ch").innerHTML = "";
        document.getElementById("chats").innerHTML = "";
        document.getElementById("c-total").innerHTML = "";
        return price;
    }

    // ===================================
}
document
    .querySelectorAll(
        'input[type="checkbox"], input[type="radio"], input[type="number"], select'
    )
    .forEach(function (element) {
        element.addEventListener("change", calPrice);
    });

function updateTotalPrice() {
    let total = 0;
    document
        .querySelectorAll('input[type="checkbox"]:checked')
        .forEach(function (checkedBox) {
            total += parseFloat(checkedBox.getAttribute("data-price"));
        });
    document.getElementById("totalAdsPrice").textContent = total.toFixed(2);
}

function additionals() {
    let quality = document.getElementById("qualityCheck");
    if (quality.checked) {
        document.getElementById("ch1").innerHTML = "Professional Check";
        document.getElementById("ch1-total").innerHTML = "$ 9.75";
    } else {
        document.getElementById("ch1").innerHTML = "";
        document.getElementById("ch1-total").innerHTML = "";
    }

    let plagiarism = document.getElementById("plagiarismReport");
    if (plagiarism.checked) {
        document.getElementById("ch2").innerHTML = "Plagiarism Report";
        document.getElementById("ch2-total").innerHTML = "$ 9.99";
    } else {
        document.getElementById("ch2").innerHTML = "";
        document.getElementById("ch2-total").innerHTML = "";
    }

    let copy = document.getElementById("copySources");
    if (copy.checked) {
        document.getElementById("ch3").innerHTML = "Copy of Sources";
        document.getElementById("ch3-total").innerHTML = "$ 14.00";
    } else {
        document.getElementById("ch3").innerHTML = "";
        document.getElementById("ch3-total").innerHTML = "";
    }

    let delivary = document.getElementById("progressiveDelivery");
    if (delivary.checked) {
        document.getElementById("ch4").innerHTML = "Progressive Delivery";
        document.getElementById("ch4-total").innerHTML = "$ 14.00";
    } else {
        document.getElementById("ch4").innerHTML = "";
        document.getElementById("ch4-total").innerHTML = "";
    }
}

function calPrice() {
    let totalCheckboxPrice = 0;
    document
        .querySelectorAll('input[type="checkbox"]:checked')
        .forEach(function (checkedBox) {
            totalCheckboxPrice += parseFloat(
                checkedBox.getAttribute("data-price")
            );
        });

    let space = checkSpacing();
    const level = handleRadioChange();
    let amount = checkLevel(level);
    const deadline = handleDeadlineChange();
    let price = checkDeadline(deadline, amount);

    if (space === "Single") {
        price *= 1.5;
    }

    const slidePrice = getSlideValue();
    const chartPrice = getChartsValue();
    let t_price = price * Number(page());
    let totalPrice = t_price + slidePrice + chartPrice;
    const categoryPrice = checkCategory(totalPrice);

    const finalTotal = totalPrice + categoryPrice + totalCheckboxPrice;

    document.getElementById("cost").innerHTML = price.toFixed(2);
    document.getElementById("total").innerHTML = t_price.toFixed(2);
    document.getElementById("finalPrice").innerHTML = finalTotal.toFixed(2);
    // document.getElementById("f-price").innerHTML = finalTotal.toFixed(2);
    document.getElementById("f-price").value = finalTotal.toFixed(2);
    // document.getElementById("t--price").value = finalTotal.toFixed(2);

    additionals();
}
