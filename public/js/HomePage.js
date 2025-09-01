
let departDatePicker = null;
let returnDatePicker = null;

function handleTrainTypeChange() {
    var returnTrain = document.getElementById('return-train');
    var oneWayTrain = document.getElementById('one-way-train');
    var returnDateInput = document.getElementById('return-date');

    var toggleSwitch = document.querySelector('.toggle-switch');

    if (returnTrain.checked) {
        toggleSwitch.style.transform = 'translateX(0)';
        returnTrain.nextElementSibling.style.color = 'aliceblue';
        oneWayTrain.nextElementSibling.style.color = 'rgba(88, 88, 88, 1)';
        returnDateInput.disabled = false;
        returnDateInput.placeholder = 'Select date';
        returnDateInput.style.opacity = '1';
        returnDateInput.style.cursor = 'pointer';
        initializeDepartDatePicker(true);
    } else if (oneWayTrain.checked) {
        toggleSwitch.style.transform = 'translateX(107%)';
        oneWayTrain.nextElementSibling.style.color = 'aliceblue';
        returnTrain.nextElementSibling.style.color = 'rgba(88, 88, 88, 1)';
        returnDateInput.disabled = true;
        returnDateInput.value = '';
        returnDateInput.placeholder = 'N/A';
        returnDateInput.style.opacity = '0.5';
        returnDateInput.style.cursor = 'not-allowed';
        initializeDepartDatePicker(false);
    }
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

function swapLocations() {
    var departInput = document.querySelector('.form-group:first-child input');
    var toInput = document.querySelector('.form-group:nth-child(3) input');
    
    var departValue = departInput.value;
    var toValue = toInput.value;
    
    departInput.value = toValue;
    toInput.value = departValue;
    
    var swapBtn = document.querySelector('.swap-btn');
    swapBtn.style.transform = 'rotate(180deg)';
    setTimeout(() => {
        swapBtn.style.transform = 'rotate(0deg)';
    }, 300);
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers for default state (Return mode)
    initializeDepartDatePicker(true);
    initializeReturnDatePicker();
    
    // Initialize slider functionality
    initializeSlider();
    
    const banners = document.querySelectorAll('.banner-block');
    const body = document.body;

    function setBodyBgFromBanner(banner) {
        const bg = banner.style.backgroundImage;

        body.style.backgroundImage = bg;
        body.style.backgroundSize = 'cover';
        body.style.backgroundPosition = 'center';
        body.style.transition = 'background 0.7s cubic-bezier(.68,-0.55,.27,1.55)';
    }

    function onScroll() {
        let found = false;
        banners.forEach(banner => {
            const rect = banner.getBoundingClientRect();
            if (rect.top < window.innerHeight/2 && rect.bottom > window.innerHeight/2 && !found) {
                setBodyBgFromBanner(banner);
                found = true;
            }
        });
        if (!found) {
            body.style.backgroundImage = '';
            body.style.background = 'linear-gradient(120deg, #181c22 0%, #23272f 100%)';
        }
    }

    window.addEventListener('scroll', onScroll);
    onScroll();

    // Newsletter subscribe form handling
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        // Inject toast container if not present
        if (!document.getElementById('message-container')) {
            const div = document.createElement('div');
            div.id = 'message-container';
            div.style.display = 'none';
            div.className = 'message-container';
            div.innerHTML = '<div id="message-content" class="message-content"><span id="message-text"></span><button onclick="(function(){document.getElementById(\'message-container\').style.display=\'none\';})()" class="close-btn">&times;</button></div>';
            document.body.appendChild(div);
        }

        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = newsletterForm.querySelector('.email-input');
            const email = emailInput.value.trim();
            if (!email) return;
            fetch('/newsletter/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ email })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('Subscribed successfully!', 'success');
                    emailInput.value = '';
                } else {
                    showToast(data.message || 'Subscription failed.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Subscription failed.', 'error');
            });
        });
    }
});

function initializeSlider() {
    const list = document.querySelector('.slider .list');
    const items = document.querySelectorAll('.slider .list .item');
    const next = document.getElementById('next');
    const prev = document.getElementById('prev');
    const thumbnails = document.querySelectorAll('.thumbnail .item');
    
    let countItem = items.length;
    let itemActive = 0;
    
    // Auto run slider
    let refreshInterval = setInterval(() => {
        next.click();
    }, 5000);
    
    // Event next click
    next.onclick = function() {
        itemActive = itemActive + 1;
        if (itemActive >= countItem) {
            itemActive = 0;
        }
        showSlider();
    }
    
    // Event prev click
    prev.onclick = function() {
        itemActive = itemActive - 1;
        if (itemActive < 0) {
            itemActive = countItem - 1;
        }
        showSlider();
    }
    
    // Remove active class from all items
    function removeActive() {
        let itemActiveOld = document.querySelector('.slider .list .item.active');
        let thumbnailActiveOld = document.querySelector('.thumbnail .item.active');
        itemActiveOld.classList.remove('active');
        thumbnailActiveOld.classList.remove('active');
    }
    
    // Add active class to current item
    function addActive() {
        items[itemActive].classList.add('active');
        thumbnails[itemActive].classList.add('active');
    }
    
    // Show slider
    function showSlider() {
        removeActive();
        addActive();
    }
    
    // Click thumbnail
    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => {
            itemActive = index;
            showSlider();
        })
    })
}

// Lightweight toast for homepage
function showToast(message, type) {
    const container = document.getElementById('message-container');
    const messageText = document.getElementById('message-text');
    const messageContent = document.getElementById('message-content');
    if (!container || !messageText || !messageContent) return;
    messageText.textContent = message;
    messageContent.className = `message-content message-${type}`;
    container.style.display = 'block';
    setTimeout(() => { container.style.display = 'none'; }, 4000);
}


