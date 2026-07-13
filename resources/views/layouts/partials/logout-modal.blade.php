{{-- DaisyUI Logout Modal --}}
<dialog id="logout_modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Konfirmasi Keluar</h3>
        <p class="py-4">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?</p>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn btn-ghost">Batal</button>
            </form>
            <button class="btn btn-error text-white"
                onclick="document.getElementById('logout-form').submit()">Ya, Keluar</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

{{-- Hidden Logout Form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
