<?php

use App\Models\Asset;
use App\Models\CsItem;
use App\Models\CsDocno;
use App\Services\ChecksheetMasterService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;

new class extends Component {
    use WithFileUploads, Toast;

    // ── Filters ──────────────────────────────────────────────────────────────
    public string $line_name = '';
    public string $asset_no  = '';

    // ── Modal toggles ────────────────────────────────────────────────────────
    public bool $addModal     = false;
    public bool $editModal    = false;
    public bool $revisiModal  = false;
    public bool $historyModal = false;

    // ── Add Item ─────────────────────────────────────────────────────────────
    public string $addItemCheck = '';
    public string $addStandard  = '';
    public string $addMethod    = '';
    public string $addPeriode   = '';
    public bool   $addIsActive  = true;
    public $addPhoto = null;

    // ── Edit Item ─────────────────────────────────────────────────────────────
    public ?int    $itemId           = null;
    public string  $editItemCheck    = '';
    public string  $editStandard     = '';
    public string  $editMethod       = '';
    public string  $editPeriode      = '';
    public bool    $editIsActive     = true;
    public ?string $editExistingPhoto = null;
    public $editPhoto = null;

    // ── Revisi ────────────────────────────────────────────────────────────────
    public string $revDocNo      = '';
    public string $revItemRevisi = '';
    public string $revKeterangan = '';
    public string $revTanggal   = '';

    // ── Data (render) ─────────────────────────────────────────────────────────
    public function with(ChecksheetMasterService $svc): array
    {
        $items = $this->asset_no ? $svc->getItems($this->asset_no) : collect();

        return [
            'lines'          => $svc->getLines(),
            'machines'       => $this->line_name ? $svc->getMachines($this->line_name) : collect(),
            'selectedAsset'  => $this->asset_no  ? $svc->getSelectedAsset($this->asset_no) : null,
            'items'          => $items,
            'stats'          => $svc->getStats($items),
            'currentDoc'     => $this->asset_no ? $svc->getCurrentDoc($this->asset_no) : null,
            'historyDocs'    => $this->asset_no ? $svc->getHistoryDocs($this->asset_no) : collect(),
            'periodeOptions' => $svc->periodeOptions(),
        ];
    }

    // ── Add ───────────────────────────────────────────────────────────────────
    public function openAddModal(): void
    {
        if (!$this->asset_no) { $this->warning('Pilih mesin terlebih dahulu.'); return; }
        $this->reset(['addItemCheck','addStandard','addMethod','addPeriode','addPhoto']);
        $this->addIsActive = true;
        $this->addModal = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'addItemCheck' => 'required|string',
            'addStandard'  => 'required|string',
            'addMethod'    => 'required|string',
            'addPeriode'   => 'required|string',
            'addPhoto'     => 'nullable|image|max:2048',
        ]);

        $machine = Asset::where('asset_no', $this->asset_no)->first();

        CsItem::create([
            'asset_no'    => $this->asset_no,
            'machine_name'=> $machine?->machine_name,
            'line_name'   => $this->line_name,
            'item_check'  => $this->addItemCheck,
            'standard'    => $this->addStandard,
            'method'      => $this->addMethod,
            'periode'     => $this->addPeriode,
            'photo_path'  => $this->addPhoto?->store('cs_photos', 'public'),
            'is_active'   => $this->addIsActive,
            'sort_order'  => CsItem::where('asset_no', $this->asset_no)->count() + 1,
        ]);

        $this->success('Item berhasil ditambahkan.');
        $this->addModal = false;
    }

    // ── Edit ──────────────────────────────────────────────────────────────────
    public function openEditItem(int $id): void
    {
        $item = CsItem::findOrFail($id);
        $this->fill([
            'itemId'            => $item->id,
            'editItemCheck'     => $item->item_check ?? '',
            'editStandard'      => $item->standard ?? '',
            'editMethod'        => $item->method ?? '',
            'editPeriode'       => $item->periode ?? '',
            'editExistingPhoto' => $item->photo_path,
            'editIsActive'      => $item->is_active,
            'editPhoto'         => null,
        ]);
        $this->editModal = true;
    }

    public function updateItem(): void
    {
        $this->validate([
            'editItemCheck' => 'required|string',
            'editStandard'  => 'required|string',
            'editMethod'    => 'required|string',
            'editPeriode'   => 'required|string',
            'editPhoto'     => 'nullable|image|max:2048',
        ]);

        $item = CsItem::findOrFail($this->itemId);
        $path = $item->photo_path;

        if ($this->editPhoto) {
            if ($path && Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
            $path = $this->editPhoto->store('cs_photos', 'public');
        }

        $item->update([
            'item_check' => $this->editItemCheck,
            'standard'   => $this->editStandard,
            'method'     => $this->editMethod,
            'periode'    => $this->editPeriode,
            'photo_path' => $path,
            'is_active'  => $this->editIsActive,
        ]);

        $this->success('Item berhasil diperbarui.');
        $this->editModal = false;
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function deleteItem(int $id): void
    {
        $item = CsItem::findOrFail($id);
        if ($item->photo_path && Storage::disk('public')->exists($item->photo_path)) {
            Storage::disk('public')->delete($item->photo_path);
        }
        $item->delete();
        $this->success('Item berhasil dihapus.');
    }

    // ── Revisi ────────────────────────────────────────────────────────────────
    public function openRevisiModal(): void
    {
        if (!$this->asset_no) { $this->warning('Pilih mesin terlebih dahulu.'); return; }
        $this->reset(['revDocNo','revItemRevisi','revKeterangan']);
        $this->revTanggal = date('Y-m-d');
        $this->revisiModal = true;
    }

    public function saveRevisi(): void
    {
        $this->validate([
            'revDocNo'      => 'required|string',
            'revItemRevisi' => 'required|string',
            'revKeterangan' => 'nullable|string',
            'revTanggal'    => 'required|date',
        ]);

        CsDocno::create([
            'asset_no'       => $this->asset_no,
            'doc_no'         => $this->revDocNo,
            'item_revisi'    => $this->revItemRevisi,
            'keterangan'     => $this->revKeterangan,
            'tanggal_revisi' => $this->revTanggal,
        ]);

        $this->success('Revisi dokumen berhasil disimpan.');
        $this->revisiModal = false;
    }

    public function deleteHistory(int $id): void
    {
        CsDocno::findOrFail($id)->delete();
        $this->success('Riwayat revisi berhasil dihapus.');
    }
};
?>

<div>
    @include('livewire.master-data.checksheet.partials.header')
    @include('livewire.master-data.checksheet.partials.filter')
    @if($asset_no)
        @include('livewire.master-data.checksheet.partials.stats')
    @endif
    @include('livewire.master-data.checksheet.partials.table')
    @include('livewire.master-data.checksheet.partials.modal-add')
    @include('livewire.master-data.checksheet.partials.modal-edit')
    @include('livewire.master-data.checksheet.partials.modal-revisi')
    @include('livewire.master-data.checksheet.partials.modal-history')
</div>
