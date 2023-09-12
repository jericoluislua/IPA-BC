/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

//Import javascript for animation
import './flipCard';
//Import javascript to remove extra classes on certain classes
//For example remove required class in input-group-text classes
import './removeExtraClasses'
//Import all font styles
import './styles/fonts/TT-Interfaces/stylesheet.css';

// any CSS you import will output into a single css file (app.scss in this case)
import "bootstrap/scss/bootstrap.scss";
import './styles/app.scss'; /*Needs to be after the default Bootstrap template to overwrite the default values correctly.*/

// start the Stimulus application
import './bootstrap';

// To make FontAwesome work add these files.
import('@fortawesome/fontawesome-free/css/all.min.css');
import('@fortawesome/fontawesome-free/js/all.js');
