let menuBtn = document.querySelector('.menu-btn');
let menu = document.querySelector('.menu');
let nav = document.querySelector('.header_nav_mobile');

menuBtn.addEventListener('click', function(){
  menuBtn.classList.toggle('active');
  menu.classList.toggle('active');
})

nav.addEventListener('click', function(){
  menuBtn.classList.remove('active');
  menu.classList.remove('active');
})
