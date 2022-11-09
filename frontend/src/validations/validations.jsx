function verifyRut( input_rut ){
    /*
Algoritmo que nos permite ingresar nuestro número de Run y generar automáticamente nuestro dígito verificador (Rol único nacional).

*/

    let rut = input_rut.value;
    let rutParts = rut.split('-');
    //Validamos que el rut esté ingresado sin el dígito verifcador
    let isValid;

    if( !Number.isNaN(rutParts[0]) && rutParts[0].length >= 7 ){
        isValid = true;
    }

    if( isValid ) {

        rut = rutParts[0].split(''); // Con el método split separamos un string en base al parámetro ingresado y lo convertimos en un array.
        // Ejemplo, sin ingresamos rut sin dígito '20217260', al nosotros pasar como parámetro '' que es un string vacío lo que hace js es separar todos los char del string y los convierte en un array --> ['2','0', ... etc]

        // Ahora que nuestro valor ingresado es un array, podemos utilzar el método reverse para invertir el array.
        rut.reverse();

        // Tomamos los números y multiplicamos cada uno de ellos por la siguiente serie de números: 2, 3, 4, 5, 6, 7… en ese orden. Volver a 2 en caso de llegar a 7.

        // Para ello declaramos e inicializamos un array con todos los valores que multiplicarán a cada número
        // del rut ingresado. En este caso lo llamamos multipliers ( muliplicadores )
        let multipliers = [2,3,4,5,6,7];
        let i = 0; // Establecemos un contador que nos ayudará de determinar si se ha llegado al número 7

        // Recorremos nuestro array 'rut' que contiene cada dígito de nuestro rut sin dígito verificador.
        rut.forEach( (element, index) => {
            /*
            Si i es mayor que 5 significa que ya se ha alcanzado el número 7 de nuestro array multipliers
            ya que 5 es el índice máximo de nuestro array. Si se cumple esa condición, reseteamos el contador
            a 0 para que vuelva a 2 el número que multiplicará a nuestro siguiente dígito de nuestro array 'rut'
            y seguimos así hasta terminar el recorrido.
            */
            if( i > 5 ){
                i = 0;
            }
            /*
            Declaramos e inicializamos la variable 'numRut' por cada vuelta del ciclo la cual
            contendrá el valor de la multiplicación del dígito actual( element ) y el multiplicador
            actual ( multipliers[i] ).
            */
            rut[index] = element * multipliers[i]; // Reasignamos el valor resultante de la variable 'numRut' a la posición correspondiente de nuestro array 'rut'.
            i++; // Incrementamos el contador i de nuestro array multipliers
        });

        // Finalmente obtenemos un array que contendrá los valores de los dígitos multiplicados por los números indicados.


        // Una vez que haya multiplicado cada uno de los números, sumamos los resultados obtenidos.
        // Utilizamos el método current de la misma forma en que lo utilizamos anteriormente.
        let sumNumbers = rut.reduce(function( previous, current ){
            return previous + current;
        });

        // Dividimos, el número obtenido, por 11 y obtenemos el resto de esa división
        let rest = sumNumbers % 11;

        // Al número 11, se le resta el resto de la división anterior y ahora debemos analizar el número obtenido. Hay tres posibilidades:
        let finalNum= 11 - rest;
        let message; // Declaramos la variable que almacenará el mensaje dependiendo del resultado obtenido
        let digitoVerificador;
        switch (finalNum){
            case 11:
                //message = 'Tu dígito verificador es: 0'
                digitoVerificador = 0;
                break;
            case 10:
                //message = 'Tu dígito verificador es: K';
                digitoVerificador = 'K';
                break;
            default:
                //message = `Tu dígito verificador es: ${finalNum}`;
                digitoVerificador = finalNum;
        }

        let isValidRut;
        isValidRut = parseInt(rutParts[1]) === parseInt(digitoVerificador);

        return isValidRut;

    }

    return false;

}

/**
 * If the input value matches the regular expression, return true, otherwise return false.
 * @returns The value of the input field.
 */
function validEmail( input ){
    let validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    return input.value.match(validRegex);
}

/**
 * If the input is not a number and the length of the input is 9, then return true.
 * @returns a boolean value.
 */
function validPhone( input ){
    return !Number.isNaN(input.value) && input.value.length === 9;
}


/**
 * It checks the password's strength and changes the badge's color and text based on the password
 * strength
 */
function validatePassword( isValid ) {
    // timeout before a callback is called

    let timeout;
    // traversing the DOM and getting the input and span using their IDs

    let password = document.getElementById('PassEntry')
    let strengthBadge = document.getElementById('StrengthDisp')

    // The strong and weak password Regex pattern checker

    let strongPassword = new RegExp('(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})')
    let mediumPassword = new RegExp('((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{6,}))|((?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9])(?=.{8,}))')

    /**
     * It checks the password's strength and changes the badge's color and text based on the password
     * strength.
     */
    function StrengthChecker(PasswordParameter){
        // We then change the badge's color and text based on the password strength

        if(strongPassword.test(PasswordParameter)) {
            if( isValid.length < 1 ) {
                isValid.push(true);
            }

            strengthBadge.style.backgroundColor = "green"
            strengthBadge.textContent = 'Fuerte'
        } else if(mediumPassword.test(PasswordParameter)){
            if( isValid.length === 1 ) {
                isValid.pop();
            }
            strengthBadge.style.backgroundColor = 'blue'
            strengthBadge.textContent = 'Mediana'
        } else{
            if( isValid.length === 1 ) {
                isValid.pop();
            }
            strengthBadge.style.backgroundColor = 'red'
            strengthBadge.textContent = 'Débil'
        }
    }

    // Adding an input event listener when a user types to the  password input

    /* Adding an event listener to the password input field. */
    password.addEventListener("input", () => {

        //The badge is hidden by default, so we show it

        strengthBadge.style.display= 'block'
        clearTimeout(timeout);

        //We then call the StrengChecker function as a callback then pass the typed password to it

        timeout = setTimeout(() => StrengthChecker(password.value), 200);

        //Incase a user clears the text, the badge is hidden again

        if(password.value.length !== 0){
            strengthBadge.style.display !== 'block'
        } else{
            strengthBadge.style.display = 'none'
        }
    });


}




document.addEventListener('DOMContentLoaded', () => {
    let buttons = (document.querySelectorAll('.rut-btn-verify') || []);
    buttons.forEach((button)=>{
        let target = button.dataset.target;
        let rutInput = document.getElementById(target);

        button.addEventListener('click', (e) =>{
            let ningunoOption = document.getElementById('ninguno');
            if( ningunoOption !== null ) {
                if( !ningunoOption.checked ) {
                    e.preventDefault();
                }else {
                    return undefined;
                }
            }
            e.preventDefault();
            let isValid = verifyRut(rutInput);

            if( isValid ) {
                let targetForm = rutInput.dataset.form;

                let isValidEmail;
                let isValidPhone;
                let isOneEmpty;
                ( document.querySelectorAll('.email-inputs') || [] ).forEach(( input )=>{
                    isValidEmail = !!validEmail(input);
                });

                ( document.querySelectorAll('.phone-inputs') || [] ).forEach(( input )=>{
                    isValidPhone = !!validPhone(input);
                });

                ( document.querySelectorAll('.prev-input') || [] ).forEach( (input)=>{

                    if( input.value === null || input.value === '' ) {
                        isOneEmpty = true;
                    }

                })

                button.disabled = true;
                if( isValidEmail === undefined && isValidPhone === undefined && isOneEmpty === undefined ){
                    button.disabled = true;
                    document.getElementById(targetForm).submit();
                }else if( isValidEmail && isValidPhone && !isOneEmpty ) {
                    button.disabled = true;
                    document.getElementById(targetForm).submit();
                }else if( isValidEmail && !isOneEmpty ){
                    button.disabled = true;
                    document.getElementById(targetForm).submit();
                }else {
                    button.disabled = false;
                    let messages = '';
                    if( !isValidEmail && isValidEmail !== undefined )  messages += '<p class="is-text has-text-danger">Email inválido</p>';
                    if( !isValidPhone && isValidPhone !== undefined ) messages += '<p class="is-text has-text-danger">Teléfono inválido</p>';
                    if( isOneEmpty ) messages += '<p class="is-text has-text-danger">Faltan campos por completar</p>';
                    document.getElementById('error').innerHTML = messages;
                }

            }else {
                document.getElementById('error').innerText = '*Rut inválido*';
            }


        })
    })


});