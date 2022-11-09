document.addEventListener('DOMContentLoaded', function () {
    // Functions to open and close a modal
    /**
     * It adds the class `is-active` to the element that is passed to it.
     * @param  - The element you want to open.
     */
    function openModal($el) {
        $el.classList.add('is-active');
    }

    /**
     * It removes the class 'is-active' from the element that is passed to it.
     * @param  - The element that you want to close.
     */
    function closeModal($el) {
        $el.classList.remove('is-active');
    }

    /**
     * Close all modals by finding all elements with the class 'modal' and then calling the closeModal
     * function on each one.
     */
    function closeAllModals() {
        (document.querySelectorAll('.modal') || []).forEach(function ($modal) {
            closeModal($modal);
        });
    }

    // Add a click event on buttons to open a specific modal
    (document.querySelectorAll('.js-modal-trigger') || []).forEach(function ($trigger) {
        var modal = $trigger.dataset.target;
        var $target = document.getElementById(modal);

        $trigger.addEventListener('click', function () {
            openModal($target);
        });
    });

    // Add a click event on various child elements to close the parent modal
    (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button') || []).forEach(function ($close) {
        var $target = $close.closest('.modal');

        $close.addEventListener('click', function () {
            closeModal($target);
        });
    });

    // Add a keyboard event to close all modals
    document.addEventListener('keydown', function (event) {
        var e = event || window.event;

        if (e.keyCode === 27) {
            // Escape key
            closeAllModals();
        }
    });
});