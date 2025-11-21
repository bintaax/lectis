module.exports = {
  content: [
    "./src/**/*.{html,js,php,twig}",
    "./templates/**/*.html.twig",
    "./assets/**/*.{js,css}",
    "./node_modules/flowbite/**/*.js"
  ],
  
  plugins: [
    require('flowbite/plugin'),
  ],
}
