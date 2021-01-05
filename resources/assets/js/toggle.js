const SidebarToggler = () => {
  // toggle sidebar when button clicked
  $('.sidebar-toggle').on('click', function (event) {
    event.preventDefault()
    $('.sidebar').toggleClass('toggled')
  })

  // auto-expand submenu if an item is active
  const active = $('.sidebar .active')

  if (active.length && active.parent('.collapse').length) {
    const parent = active.parent('.collapse')

    parent.prev('a').attr('aria-expanded', true)
    parent.addClass('show')
  }
}

export default SidebarToggler
