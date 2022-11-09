document.addEventListener('DOMContentLoaded', () => {
   let crearUsuariosContainer = document.querySelector('#jefe-mesa-usuario-1');
   let crearUsuariosOption = document.querySelector('#usuarios-1');

   let verUsuariosContainer = document.querySelector('#jefe-mesa-usuario-2');
   let verUsuariosOption = document.querySelector('#usuarios-2');

   crearUsuariosOption.addEventListener('click', ()=> {
      crearUsuariosContainer.style.display = 'block';
   });

   verUsuariosOption.addEventListener('click', () => {
      verUsuariosContainer.style.display = 'block';
   });

});
