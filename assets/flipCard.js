import $ from 'jquery';
$('.btn-flip').click(function() {
    $(this).closest('.flip-card').toggleClass('hover'); //Search for the nearest flip-card class and give it a hover class.
});