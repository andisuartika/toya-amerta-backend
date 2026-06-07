<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="fs-48 text-danger d-block mb-3"></iconify-icon>
                <h5 class="mb-1">Hapus {{ $entity }}?</h5>
                <p class="text-muted fs-13 mb-0" id="deleteItemName"></p>
                @isset($warning)
                    <p class="text-danger fs-12 mt-2 mb-0">{{ $warning }}</p>
                @endisset
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form id="formDelete" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
