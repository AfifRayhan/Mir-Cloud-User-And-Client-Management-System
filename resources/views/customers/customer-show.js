/**
 * Customer Show Page JavaScript
 * Handles PDF preview modal functionality
 */
(function () {
    const pdfPreviewModal = document.getElementById('pdfPreviewModal');
    const pdfPreviewFrame = document.getElementById('pdfPreviewFrame');
    const pdfPreviewModalLabel = document.getElementById('pdfPreviewModalLabel');
    const pdfPreviewModalSublabel = document.getElementById('pdfPreviewModalSublabel');
    const pdfDownloadBtn = document.getElementById('pdfDownloadBtn');

    // Handle preview button click
    window.previewPdf = function (url, name, size) {
        pdfPreviewModalLabel.textContent = name;
        pdfPreviewModalSublabel.textContent = `File size: ${size}`;
        pdfDownloadBtn.href = url;
        pdfDownloadBtn.setAttribute('download', name); // Force download with specific name
        pdfPreviewFrame.src = url;

        const modal = new bootstrap.Modal(pdfPreviewModal);
        modal.show();
    };

    // Clear iframe src when modal is hidden to free memory
    if (pdfPreviewModal) {
        pdfPreviewModal.addEventListener('hidden.bs.modal', function () {
            pdfPreviewFrame.src = '';
        });
    }
})();
