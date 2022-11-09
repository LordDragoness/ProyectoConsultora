document.addEventListener('DOMContentLoaded', function () {
    var password = document.querySelector('#PassEntry');
    var repeatedPassword = document.querySelector('#PassRepeatEntry');
    var changePassForm = document.querySelector('#change-pass-form');
    var formButton = document.querySelector('#pass-btn');
    var profileInfoContainer = document.querySelector('#profile-info-container');

    //Contenedor del formulario para cambiar contraseña
    var changePassFormContainer = document.querySelector('#change-pass-container');

    // Seleccionamos el contendero del aviso de cambio de contraseña
    var changePassWarning = document.querySelector('#change-password-warning');

    // This button es el que gatilla el renderizado del formulario para cambiar contraseña
    var warningPassBtn = document.querySelector('#change-pass');

    /* Hiding the warning message and showing the form to change the password. */
    warningPassBtn.addEventListener('click', function () {
        changePassWarning.style.display = 'none';
        profileInfoContainer.style.display = 'none';
        changePassFormContainer.style.display = 'flex';
    });

    var isValid = [];

    validatePassword(isValid);

    /* The event listener for the submit button. It is checking if the password is valid and if the
    repeated password is the same as the password. If it is, it disables the form and submits it. If
    it is not, it displays an error message. */
    formButton.addEventListener('click', function (e) {
        e.preventDefault();

        if (isValid[0] && password.value === repeatedPassword.value) {
            changePassForm.disabled = true;
            changePassForm.submit();
        } else {
            document.getElementById('error').innerText = 'Las contraseñas no coinciden';
        }
    });
});