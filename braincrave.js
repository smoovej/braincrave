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
});