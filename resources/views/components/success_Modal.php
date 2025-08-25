<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@if (session('success'))
    <div 
        x-data="{ 
            showModal: true,
            init() {
                // Check if we've already shown this message
                const messageKey = 'successShown_' + '{{ md5(session('success')) }}';
                
                if (sessionStorage.getItem(messageKey)) {
                    // Already shown this message, don't show again
                    this.showModal = false;
                    return;
                }
                
                // Mark this message as shown
                sessionStorage.setItem(messageKey, 'true');
                
                // Auto-hide after 8 seconds
                setTimeout(() => {
                    this.hideModal();
                }, 8000);
            },
            hideModal() {
                this.showModal = false;
                document.body.classList.remove('overflow-hidden');
                
                // Clear the session message from Laravel session
                fetch('{{ route('clear.session') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).catch(error => {
                    console.log('Session cleared');
                });
            }
        }" 
        x-init="init()"
        x-show="showModal" 
        x-transition 
        @click="hideModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
    >
        <div 
            @click.stop
            class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md text-center relative animate-fade-in-down"
        >
            <!-- Close button -->
            <button 
                @click="hideModal()" 
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-xl font-bold"
                style="font-size: 24px; line-height: 1;"
            >
                ×
            </button>
            
            <h2 class="text-xl font-semibold text-green-600 mb-4">Success</h2>

            <img src="{{ asset('images/icon/success.gif') }}" alt="Success GIF" class="w-40 h-32 mx-auto mb-4">

            <p class="text-sm text-gray-700 mb-4">
                {{ session('success') }}
            </p>

            <button 
                @click="hideModal()" 
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
            >
                Back
            </button>
        </div>
    </div>
@endif
