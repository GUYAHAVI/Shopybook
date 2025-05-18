// Prevent dropdown from closing when clicking inside
document.querySelector('.keep-open').addEventListener('click', function (event) {
    event.stopPropagation();
});

function changePeople(type, increment) {
    const countElement = document.getElementById(`${type}Count`);
    let count = parseInt(countElement.innerText);
    
    // Update the count with the increment value
    count = Math.max(0, count + increment);  // Ensures the count does not go below 0
    countElement.innerText = count;
    
    // Update total guests
    updateTotalGuests();
}

function updateTotalGuests() {
    const adultCount = parseInt(document.getElementById('adultCount').innerText);
    const youthCount = parseInt(document.getElementById('youthCount').innerText);
    const childrenCount = parseInt(document.getElementById('childrenCount').innerText);
    const total = adultCount + youthCount + childrenCount;
    
    document.getElementById('totalGuests').innerText = total;
}
