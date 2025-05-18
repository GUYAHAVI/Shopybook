document.addEventListener("DOMContentLoaded", function () {
    const text = "Professional Essay Writing Service by Experts";
    const headlineElement = document.querySelector(".headline");
    let index = 0;

    function type() {
        if (index < text.length) {
            headlineElement.textContent += text.charAt(index);
            index++;
            setTimeout(type, 100); // Adjust speed by changing the delay time (in milliseconds)
        } else {
            setTimeout(() => {
                headlineElement.textContent = "";
                index = 0;
                type();
            }, 2000); // Adjust delay before restarting (in milliseconds)
        }
    }

    type();
});
const wordsPerPage = 275;

function changePages(amount) {
    const pagesInput = document.getElementById("pages");
    let pages = parseInt(pagesInput.value) + amount;
    if (pages < 1) pages = 1;
    pagesInput.value = pages;
    updateWordsAndPrice();
}

function updateWords() {
    const pages = parseInt(document.getElementById("pages").value);
    if (isNaN(pages) || pages < 1) {
        pages = 1;
        document.getElementById("totalWords").innerText = wordsPerPage;
        document.getElementById("ttWords").value = 1 * wordsPerPage;
    } else {
        document.getElementById("totalWords").innerText = pages * wordsPerPage;
        document.getElementById("ttWords").value = pages * wordsPerPage;
    }

    document.getElementById("pagesTotal").value = pages;
}

function updateWordsAndPrice() {
    // updateWords();
    calculatePrice();
}

function calculatePrice() {
    const academicLevel = parseInt(
        document.getElementById("academicLevel").value
    );
    const deadline = parseInt(document.getElementById("deadline").value);
    const pages = parseInt(document.getElementById("pages").value);
    const essayType = parseInt(document.getElementById("essayType").value);

    if (
        isNaN(academicLevel) ||
        isNaN(deadline) ||
        isNaN(pages) ||
        isNaN(essayType)
    ) {
        document.getElementById("totalPrice").innerText = "0";
        return;
    }

    const basePrice = academicLevel + deadline + essayType;
    const totalPrice = basePrice * pages;

    document.getElementById("totalPrice").innerText = totalPrice.toFixed(2);
    document.getElementById("ttPrice").value = totalPrice;
    updateWords();
}



document.addEventListener('DOMContentLoaded', function() {
    const academicLevel = localStorage.getItem('academicLevel');
    const deadline = localStorage.getItem('deadline');
    const essayType = localStorage.getItem('essayType');
    const pages = localStorage.getItem('pages');

    if (academicLevel) {
        document.getElementById('academicLevel').value = academicLevel;
    }
    if (deadline) {
        document.getElementById('deadline').value = deadline;
    }
    if (essayType) {
        document.getElementById('essayType').value = essayType;
    }
    if (pages) {
        document.getElementById('pages').value = pages;
    }
});