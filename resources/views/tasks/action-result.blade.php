<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    @if($success)
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Success!</h2>
                        <p class="text-gray-600 mb-6">{{ $message }}</p>
                    @else
                        <div class="mb-4">
                            <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Notice</h2>
                        <p class="text-gray-600 mb-6">{{ $message }}</p>
                    @endif
                    
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
