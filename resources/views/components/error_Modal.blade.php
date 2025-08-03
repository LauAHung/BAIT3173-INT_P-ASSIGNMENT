<!-- resources/views/components/error-modal.blade.php -->

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@if ($errors->any())
    <div 
        x-data="{ showModal: true }" 
        x-init="document.body.classList.add('overflow-hidden')"
        x-show="showModal" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
    >
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md text-center relative animate-fade-in-down">
            <h2 class="text-xl font-semibold text-red-600 mb-4">Error Occurred</h2>

            <img src="{{ asset('images/icon/error.gif') }}" alt="Error GIF" class="w-50 h-32 mx-auto mb-4"> 

            <ul class="list-disc list-inside text-center text-sm text-gray-700 max-h-60 overflow-y-auto mb-4">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </ul>

            <button 
                @click="window.location.reload()" 
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
            >
                Back
            </button>
        </div>
    </div>
@endif
