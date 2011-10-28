$(document).ready(function() {
    $("#success").animate({ 
      opacity: 0.0,

    }, 3000 );

    $("#tweetsuccess").animate({ 
      opacity: 0.0,

    }, 3000 );

  $('#upload_form').hide(); 
  $('#toggle_upload_form').click(function() {
    $("#upload_form").slideToggle('slow');
  });
  
  $('div.video').embedly({
        maxWidth: 800,
        key: '9d947f12d4e411e0b41d4040d3dc5c07'
  });
});