/**
 * User Form JavaScript
 * Handles email-to-username auto-sync for create/edit forms
 */
(function () {
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');

    if (!emailInput || !usernameInput) {
        return;
    }

    const initialEmail = emailInput.value;
    const initialUsername = usernameInput.value;
    let isManuallyEdited = false;

    // For edit forms: If username already matches email, we assume it should stay in sync
    // Unless the user explicitly modifies it.
    // For create forms: initialEmail and initialUsername are both empty, so they match.
    if (initialEmail && initialUsername && initialEmail !== initialUsername) {
        isManuallyEdited = true;
    }

    usernameInput.addEventListener('input', function () {
        isManuallyEdited = true;
    });

    emailInput.addEventListener('input', function () {
        if (!isManuallyEdited) {
            usernameInput.value = emailInput.value;
        }
    });
})();
