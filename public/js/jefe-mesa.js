/* This is a JavaScript function that is executed when the DOM is loaded. */
document.addEventListener('DOMContentLoaded', function () {
   var crearUsuariosContainer = document.querySelector('#jefe-mesa-usuario-1');
   var crearUsuariosOption = document.querySelector('#usuarios-1');

   var verUsuariosContainer = document.querySelector('#jefe-mesa-usuario-2');
   var verUsuariosOption = document.querySelector('#usuarios-2');

   /* Adding an event listener to the crearUsuariosOption element. When the element is clicked, the
      crearUsuariosContainer element will be displayed. */
   crearUsuariosOption.addEventListener('click', function () {
      crearUsuariosContainer.style.display = 'block';
   });

   /* Adding an event listener to the verUsuariosOption element. When the element is clicked, the
   verUsuariosContainer element will be displayed. */
   verUsuariosOption.addEventListener('click', function () {
      verUsuariosContainer.style.display = 'block';
   });
});