<div id="userFormModal" class="modal-shell" style="display:none;">
    <div class="modal-card">
        <span class="modal-close" onclick="closeUserFormModal()">&times;</span>
        <h2 id="userFormModalTitle">Add User</h2>

        <form
            id="userForm"
            class="stack-form"
            method="POST"
            action="{{ route('dashboard.users.store') }}"
            data-index-url="{{ route('dashboard.users.index') }}"
            data-users-url="{{ url('dashboard/users') }}"
            novalidate
        >
            @csrf
            <input type="hidden" name="_method" id="userFormMethod" value="POST">

            <label for="user_first_name">First Name*</label>
            <input id="user_first_name" type="text" name="first_name" autocomplete="given-name">
            <p id="user_first_name-error" class="field-error" hidden></p>

            <label for="user_last_name">Last Name*</label>
            <input id="user_last_name" type="text" name="last_name" autocomplete="family-name">
            <p id="user_last_name-error" class="field-error" hidden></p>

            <label for="user_email">Email*</label>
            <input id="user_email" type="email" name="email" autocomplete="email">
            <p id="user_email-error" class="field-error" hidden></p>

            <label for="user_title">Title*</label>
            <input id="user_title" type="text" name="title">
            <p id="user_title-error" class="field-error" hidden></p>

            <label for="user_department">Department*</label>
            <input id="user_department" type="text" name="department">
            <p id="user_department-error" class="field-error" hidden></p>

            <div id="userRoleField" hidden>
                <label for="user_role">Role*</label>
                <select id="user_role" name="role">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <p id="user_role-error" class="field-error" hidden></p>
            </div>

            <div id="userPasswordField" hidden>
                <label for="user_password">New Password</label>
                <input id="user_password" type="password" name="password" autocomplete="new-password">
                <p class="field-hint">Leave blank to keep the current password.</p>
                <p id="user_password-error" class="field-error" hidden></p>
            </div>

            <p id="user_form-error" class="field-error" hidden></p>

            <button type="submit" id="userFormSubmit">Create User</button>
        </form>
    </div>
</div>
