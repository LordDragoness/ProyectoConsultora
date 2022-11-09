document.addEventListener('DOMContentLoaded', () => {
    // Functions to open and close a modal
    function openModal($el) {
        $el.classList.add('is-active');
    }

    /**
     * If the updateActionContainer exists, then set its display to none.
     */
    function closeModal($el) {
        $el.classList.remove('is-active');
        //cerrar formulario para actualizar las criticidades
        let updateActionContainer = document.querySelector('#update-action-container');
        if( updateActionContainer !== null ) updateActionContainer.style.display = 'none';

        // Clean inputs
        cleanInputs();
    }

    /**
     * Close all modals by finding all modals and then closing each one.
     */
    function closeAllModals() {
        (document.querySelectorAll('.modal') || []).forEach(($modal) => {
            closeModal($modal);
        });
    }

    // Add a click event on buttons to open a specific modal
    (document.querySelectorAll('.js-modal-trigger') || []).forEach(($trigger) => {
        const modal = $trigger.dataset.target;
        const $target = document.getElementById(modal);

        $trigger.addEventListener('click', () => {
            openModal($target);
        });
    });

    // Add a click event on various child elements to close the parent modal
    (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(($close) => {
        const $target = $close.closest('.modal');

        $close.addEventListener('click', () => {
            if( $close.classList.contains('do-not-close') === false ) {
                closeModal($target);
            }
        });
    });

    // Add a keyboard event to close all modals
    document.addEventListener('keydown', (event) => {
        const e = event || window.event;

        if (e.keyCode === 27) { // Escape key
            closeAllModals();
        }
    });


    // DROPDOWNS CODE WITH CLICK EVENT
    function openDropdown($el) {
        if( $el.classList.contains('is-active') ){
            closeModal($el);
        }else {
            openModal($el);
        }
    }

    // Add a click evento on buttons to open specific dropdown
    ( document.querySelectorAll('.js-dropdown-trigger') || [] ).forEach( ( $trigger ) => {
        const dropdown = $trigger.dataset.target;
        const target = document.getElementById(dropdown);

        $trigger.addEventListener('click', function (e){
           e.preventDefault();
           openDropdown(target);
        });
    });


    // CHECKBOXES TO ALLOW OR DISALLOW FORM'S INPUT NAMES
    ( document.querySelectorAll('.checks-for-name') || [] ).forEach(( $trigger ) => {
       const input = $trigger.dataset.target;
       const target = document.getElementById(input);
       const name = $trigger.dataset.name;
       const order =  $trigger.dataset.order;

       $trigger.addEventListener('change', () => {

           if( $trigger.getAttribute('name') === 'aux-radio-name' ) {
               target.disabled = false;

               if( !target.hasAttribute('name') && name !== 'disabled' ) {
                   target.setAttribute('name',name);
                   target.setAttribute('required', 'required');
               }else {
                   if( $trigger.dataset.name === 'disabled' ) {
                       target.removeAttribute('name');
                       target.disabled = true;
                   }else {
                       target.setAttribute('name',name);
                       target.setAttribute('required', 'required');
                   }

               }


           }else {


               if( target.dataset.order === "true" ) {
                   console.log('existe');
                   if( !target.hasAttribute('name') && target.checked) {
                       target.setAttribute('name',name);
                   }else {
                       if( !target.checked ){
                           target.removeAttribute('name');
                       }else {
                           target.setAttribute('name',name);
                       }

                   }



               }else if( !target.hasAttribute('name') ) {
                   target.setAttribute('name',name);
                   target.setAttribute('required', 'required');
               }else {
                   target.removeAttribute('name');
               }
           }

       });

    });

});


/**
 * This function will grab all the elements with the class 'clean-input' and set their value to an
 * empty string.
 */
function cleanInputs(){
    let inputs = document.querySelectorAll('.clean-input');

    inputs.forEach(function (input){
        input.value = '';
    });
}