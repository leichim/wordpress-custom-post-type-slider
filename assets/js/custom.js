jQuery(document).ready(function ($) {
    
    'use strict';
    
	$('.slides').slides({
		container: "slides-container",
		preload: true,
		preloadImage: 'pasteyoururl.com/assets/img/loading.gif',
		play: 10000,
		pause: 10000,
		slideSpeed: 1250,
		hoverPause: true
    });
    
});