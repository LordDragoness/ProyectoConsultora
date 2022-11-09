

document.addEventListener('DOMContentLoaded', function () {

    var baseURL = 'http://localhost:8003';
    var manageUsuarios = document.querySelector('#usuarios');
    var manageCriticidad = document.querySelector('#criticidad');

    function Form(props) {
        return React.createElement(
            'div',
            { className: 'has-text-black is-full has-background-grey-lighter has-border p-2 is-bordered' },
            React.createElement(
                'form',
                { action: baseURL + '/api/' },
                React.createElement(
                    'div',
                    { className: 'field is-grouped' },
                    React.createElement(Input, { name: 'RUT', type: 'text' }),
                    React.createElement(Input, { name: 'Correo', type: 'email' })
                ),
                React.createElement(
                    'div',
                    { className: 'field is-grouped' },
                    React.createElement(Input, { name: 'Nombre', type: 'text' }),
                    React.createElement(Input, { name: 'Apellido', type: 'text' })
                ),
                React.createElement(
                    'div',
                    { className: 'field is-grouped is-center' },
                    React.createElement(
                        'div',
                        { className: 'control' },
                        React.createElement(
                            'label',
                            { className: 'label is-size-6' },
                            'Tipo Usuario'
                        ),
                        React.createElement(
                            'div',
                            { className: 'select' },
                            React.createElement(
                                'select',
                                { className: 'input', name: 'tipo_usuario' },
                                React.createElement(
                                    'option',
                                    { value: 1 },
                                    'Ejecutivo de Mesa'
                                ),
                                React.createElement(
                                    'option',
                                    { value: 2 },
                                    'Ejecutivo de Area'
                                ),
                                React.createElement(
                                    'option',
                                    { value: 3 },
                                    'Jefe de Mesa'
                                )
                            )
                        )
                    ),
                    React.createElement(
                        'div',
                        { className: 'control' },
                        React.createElement(
                            'label',
                            { className: 'label is-size-6' },
                            '\xC1rea'
                        ),
                        React.createElement(
                            'div',
                            { className: 'select' },
                            React.createElement(
                                'select',
                                { className: 'input', name: 'area' },
                                React.createElement(
                                    'option',
                                    { value: 1 },
                                    'Servicio T\xE9cnico'
                                )
                            )
                        )
                    )
                ),
                React.createElement(
                    'div',
                    { className: 'field has-text-centered' },
                    React.createElement(
                        'div',
                        { className: 'control' },
                        React.createElement(
                            'button',
                            { className: 'button is-success m-3' },
                            'Ingresar'
                        )
                    )
                )
            )
        );
    }

    /**
     * It returns a h1 element with the text "Hola Criticidad"
     * @param props - This is the object that contains the properties that were passed to the
     * component.
     * @returns A React element.
     */
    function FormCriticidad(props) {
        return React.createElement(
            'h1',
            null,
            'Hola Criticidad'
        );
    }

    var root = ReactDOM.createRoot(document.querySelector('#root'));

    manageUsuarios.addEventListener('click', function () {

        root.render(React.createElement(Form, { uri: 'api' }));
    });

    manageCriticidad.addEventListener('click', function () {

        root.render(React.createElement(FormCriticidad, null));
    });

    var $dropdowns = getAll('.dropdown:not(.is-hoverable)');

    if ($dropdowns.length > 0) {
        $dropdowns.forEach(function ($el) {
            $el.addEventListener('click', function (event) {
                event.stopPropagation();
                $el.classList.toggle('is-active');
            });
        });

        /* Listening for a click event and then calling the closeDropdowns function. */
        document.addEventListener('click', function (event) {
            closeDropdowns();
        });
    }

    /**
     * If the user clicks anywhere on the page, close all dropdowns
     */
    function closeDropdowns() {
        $dropdowns.forEach(function ($el) {
            $el.classList.remove('is-active');
        });
    }

    // Close dropdowns if ESC pressed
    document.addEventListener('keydown', function (event) {
        var e = event || window.event;
        if (e.keyCode === 27) {
            closeDropdowns();
        }
    });

    // Functions

    /**
     * GetAll() returns an array of all the elements that match the selector.
     * @param selector - A CSS selector string.
     * @returns An array of all the elements that match the selector.
     */
    function getAll(selector) {
        return Array.prototype.slice.call(document.querySelectorAll(selector), 0);
    }
});