(function () {
    function getModal() {
        return document.getElementById('userFormModal');
    }

    function getForm() {
        return document.getElementById('userForm');
    }

    function clearFieldErrors() {
        const form = getForm();
        if (!form) {
            return;
        }

        form.querySelectorAll('.field-error').forEach((error) => {
            error.textContent = '';
            error.hidden = true;
        });

        form.querySelectorAll('.input-invalid').forEach((input) => {
            input.classList.remove('input-invalid');
        });
    }

    function showFieldErrors(errors) {
        const form = getForm();
        if (!form) {
            return;
        }

        Object.entries(errors).forEach(([field, messages]) => {
            const input = form.querySelector(`[name="${field}"]`);
            const error = form.querySelector(`#user_${field}-error`) || form.querySelector('#user_form-error');

            if (input) {
                input.classList.add('input-invalid');
            }

            if (error) {
                error.textContent = Array.isArray(messages) ? messages[0] : messages;
                error.hidden = false;
            }
        });
    }

    function setCreateMode() {
        const form = getForm();
        const title = document.getElementById('userFormModalTitle');
        const methodInput = document.getElementById('userFormMethod');
        const roleField = document.getElementById('userRoleField');
        const passwordField = document.getElementById('userPasswordField');
        const submitBtn = document.getElementById('userFormSubmit');

        form.action = form.dataset.usersUrl;
        methodInput.value = 'POST';
        title.textContent = 'Add User';
        submitBtn.textContent = 'Create User';
        roleField.hidden = true;
        passwordField.hidden = true;
        form.reset();
        clearFieldErrors();
    }

    function setEditMode(user) {
        const form = getForm();
        const title = document.getElementById('userFormModalTitle');
        const methodInput = document.getElementById('userFormMethod');
        const roleField = document.getElementById('userRoleField');
        const passwordField = document.getElementById('userPasswordField');
        const submitBtn = document.getElementById('userFormSubmit');

        form.action = `${form.dataset.usersUrl}/${user.id}`;
        methodInput.value = 'PUT';
        title.textContent = 'Edit User';
        submitBtn.textContent = 'Save Changes';
        roleField.hidden = false;
        passwordField.hidden = false;

        form.querySelector('#user_first_name').value = user.first_name || '';
        form.querySelector('#user_last_name').value = user.last_name || '';
        form.querySelector('#user_email').value = user.email || '';
        form.querySelector('#user_title').value = user.title || '';
        form.querySelector('#user_department').value = user.department || '';
        form.querySelector('#user_role').value = user.role || 'user';
        form.querySelector('#user_password').value = '';

        clearFieldErrors();
    }

    window.openCreateUserModal = function () {
        setCreateMode();
        const modal = getModal();
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');
    };

    window.openEditUserModal = function (user) {
        setEditMode(user);
        const modal = getModal();
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');
    };

    window.closeUserFormModal = function () {
        const modal = getModal();
        if (modal) {
            modal.style.display = 'none';
        }
        document.body.classList.remove('modal-open');
        setCreateMode();
    };

    document.addEventListener('DOMContentLoaded', () => {
        const form = getForm();
        if (!form) {
            return;
        }

        document.querySelectorAll('.user-edit-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const user = JSON.parse(button.getAttribute('data-user'));
                openEditUserModal(user);
            });
        });

        form.addEventListener('input', clearFieldErrors);
        form.addEventListener('change', clearFieldErrors);

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFieldErrors();

            const formData = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.head.querySelector('[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok) {
                    const indexUrl = form.dataset.indexUrl;
                    const successMessage = data.message || 'Saved successfully.';
                    window.location.href = `${indexUrl}?success=${encodeURIComponent(successMessage)}`;
                    return;
                }

                if (res.status === 422 && data.errors) {
                    showFieldErrors(data.errors);
                    return;
                }

                const formError = document.getElementById('user_form-error');
                if (formError) {
                    formError.textContent = data.message || 'Unable to save user.';
                    formError.hidden = false;
                }
            } catch (error) {
                const formError = document.getElementById('user_form-error');
                if (formError) {
                    formError.textContent = 'An error occurred. Please try again.';
                    formError.hidden = false;
                }
            }
        });
    });
})();
