// document.addEventListener("DOMContentLoaded", function () {
//     const orderForm = document.getElementById("orderForm");

//     orderForm.addEventListener("submit", function (event) {
//         event.preventDefault();
//         const formData = new FormData(orderForm);
//         const validationErrors = validateForm(formData);
//         if (validationErrors.length > 0) {
//             alert(
//                 "Please correct the following errors:\n" +
//                     validationErrors.join("\n")
//             );
//             return;
//         }
//         submitForm(orderForm, formData);
//     });

//     document
//         .querySelectorAll(
//             "#orderForm input, #orderForm select, #orderForm textarea"
//         )
//         .forEach((element) => {
//             element.addEventListener("change", calculatePrice);
//             element.addEventListener("input", calculatePrice);
//         });

//     calculatePrice();

//     function validateForm(formData) {
//         const requiredFields = [
//             "academic_level",
//             "deadline",
//             "number_of_pages",
//             "paper_type",
//         ];
//         const errors = requiredFields.reduce((acc, field) => {
//             if (!formData.get(field))
//                 acc.push(`${field.replace(/_/g, " ")} is required.`);
//             return acc;
//         }, []);

//         const numberOfPages = formData.get("number_of_pages");
//         if (
//             numberOfPages &&
//             (isNaN(numberOfPages) || parseInt(numberOfPages) <= 0)
//         ) {
//             errors.push("Number of pages must be a number greater than 0.");
//         }

//         const coupon = formData.get("coupon");
//         if (coupon && !/^[a-zA-Z0-9]{5,10}$/.test(coupon)) {
//             errors.push("Coupon code must be 5-10 alphanumeric characters.");
//         }

//         return errors;
//     }

//     function calculatePrice() {
//         // Retrieve values from the first set of inputs
//         const academicLevelGroup = parseInt(
//             document.getElementById("academicLevelGroup")?.value
//         );
//         const deadline = parseInt(document.getElementById("deadline")?.value);
//         const pages = parseInt(document.getElementById("pages")?.value) || 1;
//         const typeofpaper = parseInt(
//             document.getElementById("typeofpaper")?.value
//         );

//         // Retrieve values from the second set of inputs
//         const academicLevel = document.querySelector(
//             'input[name="academic_level"]:checked'
//         )?.value;
//         const writerCategory =
//             document.getElementById("writerCategory")?.value || "Standard";
//         const slides = parseInt(document.getElementById("slides")?.value) || 0;
//         const charts = parseInt(document.getElementById("charts")?.value) || 0;

//         // Ensure that all necessary values are present and valid
//         if (
//             isNaN(academicLevelGroup) ||
//             isNaN(deadline) ||
//             isNaN(pages) ||
//             isNaN(typeofpaper) ||
//             !academicLevel
//         ) {
//             document.getElementById("totalPrice").innerText = "0";
//             return;
//         }

//         // Mapping for paper types and price per page based on academic level and deadline
//         const paperTypeMapping = {
//             "Discussion Essay": 10,
//             "Case study": 12,
//             "Research Paper": 14,
//             "Analysis(anytype)": 16,
//             "Presentation(PPT)": 18,
//             Other: 20,
//         };

//         const priceMapping = {
//             "High School": [10, 12, 14],
//             "Undergrad (Yr 1-2)": [12, 14, 16],
//             "Undergrad (Yr 3-4)": [14, 16, 18],
//             "Master's": [16, 18, 20],
//             PhD: [18, 20, 22],
//         };

//         // Calculate base prices
//         const basePriceFromGroup = academicLevelGroup + deadline + typeofpaper;
//         const basePriceFromMapping =
//             priceMapping[academicLevel][
//                 deadline === "1 Week" ? 0 : deadline === "3 Days" ? 1 : 2
//             ];

//         const calculatedBasePrice =
//             basePriceFromGroup + basePriceFromMapping * pages;

//         // Calculate additional features price
//         const writerCategoryPrice =
//             writerCategory === "Advanced"
//                 ? 0.25 * calculatedBasePrice
//                 : writerCategory === "Best_available"
//                 ? 0.3 * calculatedBasePrice
//                 : 0;
//         const additionalFeaturesPrice =
//             writerCategoryPrice + slides * 13 + charts * 13;

//         // Calculate total price
//         const totalPrice = calculatedBasePrice + additionalFeaturesPrice;

//         // Update the total price in the DOM
//         document.getElementById(
//             "totalPrice"
//         ).innerText = `$${totalPrice.toFixed(2)}`;
//         document.getElementById("ttPrice").value = totalPrice;

//         updateWords();
//         updateOrderSummary(totalPrice);
//     }

//     function updateOrderSummary(totalPrice) {
//         const orderSummaryElement = document.getElementById("orderSummary");
//         const formData = new FormData(orderForm);
//         orderSummaryElement.innerHTML = "";

//         formData.forEach((value, key) => {
//             if (key !== "_token") {
//                 const summaryItem = document.createElement("div");
//                 summaryItem.classList.add("summary-item");
//                 summaryItem.innerHTML = `<strong>${formatKey(
//                     key
//                 )}:</strong> ${value}`;
//                 orderSummaryElement.appendChild(summaryItem);
//             }
//         });

//         if (totalPrice !== undefined) {
//             const priceSummaryItem = document.createElement("div");
//             priceSummaryItem.classList.add("summary-item");
//             priceSummaryItem.innerHTML = `<strong>Total Price:</strong> $${totalPrice.toFixed(
//                 2
//             )}`;
//             orderSummaryElement.appendChild(priceSummaryItem);
//         }
//     }

//     function formatKey(key) {
//         return key.replace(/_/g, " ").replace(/\b\w/g, (l) => l.toUpperCase());
//     }
// });

function toggleTextbox(selectId, otherValue) {
    const selectElem = document.getElementById(selectId);
    const otherTextBoxId =
        selectId === "typeofpaper" ? "otherTypeBox" : "otherDisciplineBox";
    document.getElementById(otherTextBoxId).style.display =
        selectElem.value === otherValue ? "block" : "none";
}

document.querySelectorAll(".next-button").forEach((button) => {
    button.addEventListener("click", function () {
        const currentStage = parseInt(this.dataset.currentStage);
        if (validateStage(currentStage)) nextStage(currentStage + 1);
    });
});

document.querySelectorAll(".prev-button").forEach((button) => {
    button.addEventListener("click", function () {
        prevStage(parseInt(this.dataset.currentStage) - 1);
    });
});

function validateStage(stage) {
    return [
        ...document.querySelectorAll(
            `#stage${stage} input, #stage${stage} select`
        ),
    ].every(
        (input) =>
            input.value ||
            alert("Please fill out all fields before proceeding.")
    );
}

function nextStage(stage) {
    document.querySelector(".form-stage.active").classList.remove("active");
    document.getElementById(`stage${stage}`).classList.add("active");
    if (stage === 4) generateOrderSummary();
}

function prevStage(stage) {
    nextStage(stage);
}
const wordsPerPage = 275;

function changePages(amount) {
    const pagesInput = document.getElementById("pages");
    let pages = parseInt(pagesInput.value, 10) + amount;
    if (pages < 1) pages = 1;
    pagesInput.value = pages;
    updateWordsAndPrice();
}

function updateWords() {
    let pages = parseInt(document.getElementById("pages").value, 10);
    if (isNaN(pages) || pages < 1) {
        pages = 1;
    }
    const totalWords = pages * wordsPerPage;
    document.getElementById("totalWords").innerText = totalWords;
    document.getElementById("ttWords").value = totalWords;
    document.getElementById("pagesTotal").value = pages;
}

function updateWordsAndPrice() {
    updateWords();
    calculatePrice();
}

function calculatePrice() {
    const academicLevel = parseInt(document.getElementById("academicLevel").value, 10);
    const deadline = parseInt(document.getElementById("deadline").value, 10);
    const pages = parseInt(document.getElementById("pages").value, 10);
    const essayType = parseInt(document.getElementById("essayType").value, 10);

    if (isNaN(academicLevel) || isNaN(deadline) || isNaN(pages) || isNaN(essayType)) {
        document.getElementById("totalPrice").innerText = "0.00";
        return;
    }

    const basePrice = academicLevel + deadline + essayType;
    const totalPrice = basePrice * pages;

    document.getElementById("totalPrice").innerText = totalPrice.toFixed(2);
    document.getElementById("ttPrice").value = totalPrice;

    console.log(totalPrice); // Corrected log statement
}


