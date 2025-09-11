function swapLocations() {
    var departInput = document.querySelector('input[name="fromlocation"]');
    var toInput = document.querySelector('input[name="tolocation"]');
    var swapBtn = document.querySelector('.swap-btn');
    
    var departValue = departInput.value;
    var toValue = toInput.value;
    
    departInput.value = toValue;
    toInput.value = departValue;
    
    swapBtn.style.transform = 'rotate(180deg)';
    setTimeout(() => {
        swapBtn.style.transform = 'rotate(0deg)';
    }, 300);
}

flatpickr("#depart-date", {
    minDate: "today",
    dateFormat: "M d, Y",
    onChange: function(selectedDates, dateStr, instance) {
        if (dateStr) {
            document.getElementById('return-date').disabled = document.querySelector('input[name="booking_type"]').value === 'OneWay';
            flatpickr("#return-date", {
                minDate: "today" || dateStr,
                dateFormat: "M d, Y"
            });
        }
    },
    disable: [
        function(date) {
            // Disable depart-date input for return journey selection
            return ;
        }
    ]
});