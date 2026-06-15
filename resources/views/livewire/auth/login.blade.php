<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.guest')] class extends Component {
    public string $username = '';
    public string $password = '';
    public string $error = '';

    public function login(): void
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Support login via username or email
        $field = filter_var($this->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $this->username, 'password' => $this->password])) {
            session()->regenerate();
            session()->flash('success_login', 'Berhasil masuk! Selamat datang, ' . Auth::user()->name . '.');
            $this->redirect('/dashboard');
        } else {
            $this->error = 'Username or Password is incorrect!';
        }
    }
};
?>

<div class="min-h-screen flex flex-col md:flex-row w-full m-0 absolute top-0 left-0 bg-base-100">

    {{-- Left Side: Branding / Illustration (Hidden on mobile) --}}
    <div
        class="hidden md:flex flex-1 bg-primary text-primary-content items-center justify-center relative overflow-hidden">

        {{-- Background Image --}}
        <img src="{{ asset('images/company-bg.jpg') }}" alt="Company Background"
            class="absolute inset-0 w-full h-full object-cover opacity-40" />
        {{-- Optional: Soft gradient to ensure text readability --}}
        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent"></div>

        {{-- Decorative Gradients/Shapes --}}
        <div class="absolute top-[-20%] left-[-10%] w-96 h-96 bg-white rounded-full opacity-10"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-80 h-80 bg-secondary rounded-full opacity-30"></div>

        <div class="relative z-10 p-12 max-w-xl">
            <div
                class="inline-flex items-center justify-center w-28 h-28 rounded-2xl bg-white text-white mb-6 shadow-xl border border-white/10">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-20 h-20 object-contain" />
            </div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">DIGIMON</h1>
            <p class="text-lg font-medium opacity-90 mb-6">Digitalization & Monitoring System</p>
            <div class="h-1 w-20 bg-white/30 rounded mb-8"></div>
            <p class="text-primary-content/70 leading-relaxed">
                Streamline your industrial maintenance, track assets securely, and manage your workforce with a
                powerful, integrated digital platform.
            </p>
        </div>
    </div>

    {{-- Right Side: Login Form --}}
    <div class="flex-1 flex items-center justify-center p-8 md:p-12 relative overflow-hidden">
        {{-- Soft background blur for the form side --}}
        <div
            class="absolute top-[-10%] right-[-10%] w-64 h-64 bg-primary/10 rounded-full opacity-50 pointer-events-none">
        </div>

        <div class="w-full max-w-sm z-10">
            <div class="text-center md:text-left mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="md:hidden w-24 h-24 object-contain mx-auto mb-6" />
                <h3 class="font-bold text-3xl mb-2 text-base-content tracking-tight">Welcome back</h3>
                <p class="text-sm text-base-content/60">Please enter your credentials to continue to your account.</p>
            </div>

            @if($error)
                <div class="alert alert-error text-sm py-2 mb-6 shadow-sm rounded-lg">
                    <x-icon name="o-exclamation-circle" class="w-5 h-5" />
                    <span>{{ $error }}</span>
                </div>
            @endif

            <form wire:submit="login" class="space-y-5">
                <x-input label="Username / Email" wire:model="username" placeholder="admin@example.com" icon="o-user"
                    required class="input-bordered focus:input-primary" />
                <div class="relative">
                    <x-password label="Password" wire:model="password" placeholder="••••••••"
                        icon="o-lock-closed" right required class="input-bordered focus:input-primary" />
                    <div class="absolute right-0 top-0 text-[11px] mt-2">
                        <a href="#" class="link link-hover link-primary font-medium">Forgot password?</a>
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="btn btn-primary w-full shadow-lg shadow-primary/20" wire:loading.attr="disabled">
                        <span wire:loading wire:target="login" class="loading loading-spinner"></span>
                        <span wire:loading.remove wire:target="login">Sign in</span>
                        <span wire:loading wire:target="login">Signing in...</span>
                        <x-icon wire:loading.remove wire:target="login" name="o-arrow-right" class="w-5 h-5 ml-1" />
                    </button>
                </div>
            </form>

            <div class="text-center mt-10 text-[11px] text-base-content/40">
                &copy; {{ date('Y') }} CMMS Industrial Scale. All rights reserved.
            </div>
        </div>
    </div>

</div>