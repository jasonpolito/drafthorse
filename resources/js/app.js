import VanillaScrollspy from 'vanillajs-scrollspy'

const scrollspyMenus = document.querySelectorAll('[data-scrollspy]');
scrollspyMenus.forEach(menu => {
    const scrollspy = VanillaScrollspy({ menu })
    scrollspy.init()
})