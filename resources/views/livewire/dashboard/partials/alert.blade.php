@if(session('success_login'))
    <div class="alert alert-success mb-6 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.duration.500ms>
        <x-icon name="o-check-circle" class="w-6 h-6" />
        <span class="font-medium">{{ session('success_login') }}</span>
        <div>
            <button class="btn btn-sm btn-circle btn-ghost" @click="show = false"><x-icon name="o-x-mark" /></button>
        </div>
    </div>
@endif
