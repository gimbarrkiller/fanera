let scrollpos = window.scrollY

const header = document.querySelector(".go_top")
const scrollChange = 500

const add_class_on_scroll = () => header.classList.add("go_top_block")
const remove_class_on_scroll = () => header.classList.remove("go_top_block")

window.addEventListener('scroll', function() {
  scrollpos = window.scrollY;

  if (scrollpos >= scrollChange) { add_class_on_scroll() }
  else { remove_class_on_scroll() }

})
