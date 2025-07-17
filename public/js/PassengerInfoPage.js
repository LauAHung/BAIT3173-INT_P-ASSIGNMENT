document.querySelector('form').addEventListener('submit', function (e) {

    e.preventDefault(); // Prevent default form submission initially

    const mykad = document.getElementById('mykad').value.trim();
    const passport = document.getElementById('passport').value.trim();
    const passportExpiry = document.getElementById('passportExpiry').value.trim();

    // Clear any existing required flags
    document.getElementById('mykad').required = false;
    document.getElementById('passport').required = false;
    document.getElementById('passportExpiry').required = false;

    // Case 1: Both MyKad and passport are entered
    if (mykad !== '' && passport !== '') {
        alert('Please enter either MyKad number or Passport number only.');
        e.preventDefault();
        return;
    }

    // Case 2: Neither MyKad nor passport is entered
    if (mykad === '' && passport === '') {
        alert('Please enter either MyKad number or Passport number');
        e.preventDefault();
        return;
    }

    // Case 3: MyKad is entered
    if (mykad !== '') {
        if (passportExpiry !== '') {
            alert('Please clear the passport expiry date since MyKad is provided.');
            e.preventDefault();
            return;
        }
    }

    // Case 4: Passport is entered
    if (passport !== '') {
        if (passportExpiry === '') {
            alert('Please enter the passport expiry date.');
            e.preventDefault();
            return;
        }
    }

    const selectseatUrl = document.querySelector('Form').dataset.selectseatUrl;
    window.location.href = selectseatUrl;
});