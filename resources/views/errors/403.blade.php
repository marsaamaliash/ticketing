<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white shadow-md rounded-lg p-8 text-center">
            <div class="text-6xl font-bold text-red-600 mb-4">403</div>
            <h1 class="text-xl font-semibold text-gray-800 mb-2">Akses Ditolak</h1>
            <p class="text-gray-600 mb-6">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded text-sm">Kembali ke Dashboard</a>
        </div>
    </div>
</x-guest-layout>
