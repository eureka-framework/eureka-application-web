//~ Vendors Libs
import './bootstrap.index.js';
import { Application } from "@hotwired/stimulus"

//~ Stimulus controllers
import ThemeController from "./controllers/theme_controller.js";


//~ Stimulus controllers autoloader
window.Stimulus = Application.start()
Stimulus.register('theme', ThemeController);

document.addEventListener('DOMContentLoaded', () => {
});
