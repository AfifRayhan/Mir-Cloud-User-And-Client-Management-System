/**
 * Customer Edit Page JavaScript
 * Handles PO project sheet removal with modal confirmation
 */
(function () {
    let sheetIndexToDelete = null;
    let deleteSheetModal = null;
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // Attach function to window so onclick works
    window.removeExistingSheet = function (index) {
        if (!deleteSheetModal) {
            deleteSheetModal = new bootstrap.Modal(document.getElementById('deleteSheetModal'));
        }
        sheetIndexToDelete = index;
        deleteSheetModal.show();
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function () {
            if (sheetIndexToDelete !== null) {
                const item = document.getElementById(`sheet-item-${sheetIndexToDelete}`);
                const input = document.getElementById(`removed-sheet-${sheetIndexToDelete}`);

                if (item && input) {
                    item.style.opacity = '0.4';
                    item.style.pointerEvents = 'none'; // Prevent clicking again
                    input.value = sheetIndexToDelete;
                    input.removeAttribute('disabled');
                }

                if (deleteSheetModal) {
                    deleteSheetModal.hide();
                }
                sheetIndexToDelete = null;
            }
        });
    }
})();
