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

function initializeDepartDatePicker(isRange) {
    const departInput = document.getElementById('depart-date');
    
    if (departDatePicker) {
        departDatePicker.destroy();
    }
    
    departInput.value = '';
    
    const config = {
        dateFormat: "M d, Y",
        minDate: "today",
        disableMobile: true,
        theme: "material_blue",
        onChange: function(selectedDates, dateStr, instance) {
            if (isRange && selectedDates.length === 2) {
                const startDate = selectedDates[0];
                const endDate = selectedDates[1];
                const startStr = startDate.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
                const endStr = endDate.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
                departInput.value = startStr;
                
                const returnInput = document.getElementById('return-date');
                returnInput.value = endStr;
            } else if (!isRange && selectedDates.length === 1) {
                const date = selectedDates[0];
                const dateStr = date.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
                departInput.value = dateStr;
            }
        }
    };
    
    if (isRange) {
        config.mode = "range";
        config.placeholder = "Select departure and return dates";
    } else {
        config.mode = "single";
        config.placeholder = "Select departure date";
    }
    
    departDatePicker = flatpickr(departInput, config);
}

function initializeReturnDatePicker() {
    const returnInput = document.getElementById('return-date');
    
    // Destroy existing instance if it exists
    if (returnDatePicker) {
        returnDatePicker.destroy();
    }
    
    const config = {
        dateFormat: "M d, Y",
        minDate: "today",
        disableMobile: true,
        theme: "material_blue",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 1) {
                const date = selectedDates[0];
                const dateStr = date.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
                returnInput.value = dateStr;
            }
        }
    };
    
    returnDatePicker = flatpickr(returnInput, config);
}