var control_timeout, footerHeight;
$(document).foundation();
$(document).ready(function(){
	$("html").niceScroll({ autohidemode: false });
	$('#menu').localScroll({hash:true, onAfterFirst:function(){$('html, body').scrollTo( {top:'-=25px'}, 'fast' );}});
	$('.flexslider').flexslider({
      animation: "fade",
      directionNav: true,
      controlNav: false,
      pauseOnAction: true,
      pauseOnHover: true,
      direction: "horizontal",
      slideshowSpeed: 5500
    });
	
	$('#button-send').click(function(event){
		$('#button-send').html('Sending ...');
		event.preventDefault();
		$.ajax({
			type: 'POST',
			url: 'index.php/welcome/send_message',
			data: $('#contact_form').serialize(),
			success: function(html) {
				if(html == '1')
				{
					$('#button-send').html('Submit');
					$('#success').show();
					$('.reset').val('');
				}
				else
				{
					$('#button-send').html('Submit');
					$('#error').show();
				}					
			},
			error: function(){
				$('#button-send').html('Submit');
				$('#error').show();
			}
		});
		
	});
	
	
});


function valemail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
