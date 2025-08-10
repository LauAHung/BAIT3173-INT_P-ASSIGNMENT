<!-- resources/views/components/login_required_modal.blade.php -->

<div id="loginRequiredModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="hideLoginRequiredModal()">&times;</span>
        <h2 class="text-xl font-semibold text-orange-600 mb-4">Login Required</h2>

                 <img src="{{ asset('images/icon/error.gif') }}" alt="Login Required GIF" class="w-30 h-32 mx-auto mb-4"> 

        <p class="text-sm text-gray-700 mb-6">
            You need to login or register first to access your account.
        </p>

                 <div class="modal-buttons">
             <button 
                 onclick="window.location.href='{{ route('signup') }}'" 
                 class="modal-btn register-btn"
             >
                 Register
             </button>
             <button 
                 onclick="window.location.href='{{ route('signin') }}'" 
                 class="modal-btn login-btn"
             >
                 Login
             </button>
         </div>
    </div>
</div>

<script>
function showLoginRequiredModal() {
    const modal = document.getElementById('loginRequiredModal');
    const modalContent = modal.querySelector('.modal-content');
    
    // Reset any existing animation classes
    modal.classList.remove('modal-fade-in', 'modal-fade-out');
    modalContent.classList.remove('modal-content-slide-in', 'modal-content-slide-out');
    
    // Show modal first
    modal.style.display = 'block';
    
    // Force a reflow to ensure the display change takes effect
    modal.offsetHeight;
    
    // Add animation classes
    modal.classList.add('modal-fade-in');
    modalContent.classList.add('modal-content-slide-in');
}

function hideLoginRequiredModal() {
    const modal = document.getElementById('loginRequiredModal');
    const modalContent = modal.querySelector('.modal-content');
    
    // Add hide animation classes
    modal.classList.add('modal-fade-out');
    modalContent.classList.add('modal-content-slide-out');
    
    // Wait for animation to complete before hiding
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.remove('modal-fade-in', 'modal-fade-out');
        modalContent.classList.remove('modal-content-slide-in', 'modal-content-slide-out');
    }, 300);
}

// Close modal when clicking outside
window.onclick = function(event) {
    var modal = document.getElementById('loginRequiredModal');
    if (event.target == modal) {
        hideLoginRequiredModal();
    }
}
</script>

<style>
 .modal {
     display: none;
     position: fixed;
     z-index: 1000;
     left: 0;
     top: 0;
     width: 100%;
     height: 100%;
     background-color: rgba(0,0,0,0);
     transition: background-color 0.3s ease;
 }

 .modal-content {
     background-color: white;
     margin: 15% auto;
     padding: 20px;
     border-radius: 10px;
     width: 60%;
     max-width: 500px;
     position: relative;
     text-align: center;
     transform: scale(0.7) translateY(-50px);
     opacity: 0;
     transition: all 0.3s ease;
 }

 /* 淡入动画 */
 .modal-fade-in {
     background-color: rgba(0,0,0,0.5) !important;
 }

 .modal-content-slide-in {
     transform: scale(1) translateY(0) !important;
     opacity: 1 !important;
 }

 /* 淡出动画 */
 .modal-fade-out {
     background-color: rgba(0,0,0,0) !important;
 }

 .modal-content-slide-out {
     transform: scale(0.7) translateY(-50px) !important;
     opacity: 0 !important;
 }

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 10px;
    top: 10px;
}

.close:hover {
    color: black;
}

 .modal-buttons {
     display: flex;
     gap: 15px;
     justify-content: center;
     margin-top: 20px;
 }

 .modal-btn {
     background-color: #6d54b5;
     border: none;
     padding: 12px 24px;
     border-radius: 7px;
     letter-spacing: 2px;
     color: white;
     font-weight: 700;
     text-transform: uppercase;
     cursor: pointer;
     transition: 0.5s;
     transition-property: box-shadow;
     box-shadow: 0 0 25px #6d54b5;
     font-size: 14px;
     min-width: 120px;
 }

 .modal-btn:hover {
     box-shadow: 0 0 5px #6d54b5,
                 0 0 25px #6d54b5,
                 0 0 50px #6d54b5,
                 0 0 100px #6d54b5;
 }

 .register-btn {
     background-color: #6d54b5;
     box-shadow: 0 0 25px #6d54b5;
 }

 .register-btn:hover {
     box-shadow: 0 0 5px #6d54b5,
                 0 0 25px #6d54b5,
                 0 0 50px #6d54b5,
                 0 0 100px #6d54b5;
 }

 .login-btn {
     background-color: #6d54b5;
     box-shadow: 0 0 25px #6d54b5;
 }

 .login-btn:hover {
     box-shadow: 0 0 5px #6d54b5,
                 0 0 25px #6d54b5,
                 0 0 50px #6d54b5,
                 0 0 100px #6d54b5;
 }

.text-xl {
    font-size: 1.25rem;
    line-height: 1.75rem;
}

.font-semibold {
    font-weight: 600;
}

.text-orange-600 {
    color: #ea580c;
}

.mb-4 {
    margin-bottom: 16px;
}

.mb-6 {
    margin-bottom: 24px;
}

.text-sm {
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.text-gray-700 {
    color: #374151;
}

.w-50 {
    width: 200px;
}

.h-32 {
    height: 128px;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}
</style>
