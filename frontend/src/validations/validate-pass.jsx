document.addEventListener('DOMContentLoaded', () =>{
    const password = document.querySelector('#PassEntry');
    const repeatedPassword = document.querySelector('#PassRepeatEntry');
    const changePassForm = document.querySelector('#change-pass-form');
    const formButton = document.querySelector('#pass-btn');
    const profileInfoContainer = document.querySelector('#profile-info-container');

    //Contenedor del formulario para cambiar contraseña
    const changePassFormContainer = document.querySelector('#change-pass-container');

    // Seleccionamos el contendero del aviso de cambio de contraseña
    const changePassWarning = document.querySelector('#change-password-warning');

    // This button es el que gatilla el renderizado del formulario para cambiar contraseña
    const warningPassBtn = document.querySelector('#change-pass');

    /* Hiding the warning message and showing the form to change the password. */
    warningPassBtn.addEventListener('click', () => {
        changePassWarning.style.display = 'none';
        profileInfoContainer.style.display = 'none';
        changePassFormContainer.style.display = 'flex';
    });

    let isValid = [];





    validatePassword( isValid );

    /* A function that is called when the button is clicked. */
    formButton.addEventListener('click', (e)=> {
        e.preventDefault();

        if( isValid[0] && ( password.value === repeatedPassword.value  ) ) {
            changePassForm.disabled = true;
            changePassForm.submit();
        }else {
            document.getElementById('error').innerText = 'Las contraseñas no coinciden';
        }

    })

});