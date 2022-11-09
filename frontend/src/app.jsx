

document.addEventListener('DOMContentLoaded', () => {

    let baseURL = 'http://localhost:8003';
    let manageUsuarios = document.querySelector('#usuarios');
    let manageCriticidad = document.querySelector('#criticidad');

    function Form( props ) {
        return(
            <div className={'has-text-black is-full has-background-grey-lighter has-border p-2 is-bordered'}>
                <form action={baseURL + '/api/'}>
                    <div className={'field is-grouped'}>
                        < Input name={'RUT'} type={'text'}/>
                        < Input name={'Correo'} type={'email'} />
                    </div>
                    <div className={'field is-grouped'}>
                        < Input name={'Nombre'} type={'text'}/>
                        < Input name={'Apellido'} type={'text'} />
                    </div>
                    <div className={'field is-grouped is-center'}>
                        <div className={'control'}>
                            <label className={'label is-size-6'}>Tipo Usuario</label>
                            <div className={'select'}>
                                <select className={'input'} name={'tipo_usuario'}>
                                    <option value={1}>Ejecutivo de Mesa</option>
                                    <option value={2}>Ejecutivo de Area</option>
                                    <option value={3}>Jefe de Mesa</option>
                                </select>
                            </div>
                        </div>
                        <div className={'control'}>
                            <label className={'label is-size-6'}>Área</label>
                            <div className={'select'}>
                                <select className={'input'} name={'area'}>
                                    <option value={1}>Servicio Técnico</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div className={'field has-text-centered'}>
                        <div className={'control'}>
                            <button className={'button is-success m-3'} >Ingresar</button>
                        </div>
                    </div>
                </form>
            </div>

        );
    }

    /**
     * It returns a h1 element with the text "Hola Criticidad" inside it.
     * @returns A React component.
     */
    function FormCriticidad( props ) {
        return (
          <h1>Hola Criticidad</h1>
        );
    }

    const root = ReactDOM.createRoot(document.querySelector('#root'));

    manageUsuarios.addEventListener('click',() => {

        root.render(< Form uri={'api'} />);

    });

    manageCriticidad.addEventListener('click', () => {

        root.render( <FormCriticidad /> );
    })


    var $dropdowns = getAll('.dropdown:not(.is-hoverable)');

    if ($dropdowns.length > 0) {
        $dropdowns.forEach(function ($el) {
            $el.addEventListener('click', function (event) {
                event.stopPropagation();
                $el.classList.toggle('is-active');
            });
        });

        document.addEventListener('click', function (event) {
            closeDropdowns();
        });
    }

    /**
     * If the dropdown is active, remove the class 'is-active' from the dropdown.
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
     * GetAll() is a function that takes a selector as an argument and returns an array of all the
     * elements that match that selector.
     * @returns An array of all the elements that match the selector.
     */
    function getAll(selector) {
        return Array.prototype.slice.call(document.querySelectorAll(selector), 0);
    }

});