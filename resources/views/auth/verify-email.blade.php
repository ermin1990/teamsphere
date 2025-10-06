<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
            {{ __('messages.auth.verify_email.title') }}
        </h2>
        <p class="text-gray-400">{{ __('messages.auth.verify_email.subtitle') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 rounded-xl">
            <p class="text-green-400 text-sm">{{ __('messages.auth.verify_email.verification_link_sent') }}</p>
        </div>
    @endif

    <div class="flex items-center justify-center space-x-4 mt-6">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 font-medium">
                {{ __('messages.auth.verify_email.resend_verification_email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-gray-400 hover:text-white transition-colors duration-200 underline">
                {{ __('messages.auth.verify_email.log_out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
