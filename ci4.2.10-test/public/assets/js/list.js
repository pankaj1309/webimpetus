$('.table-listing-items  tr  td').on('click' , function(e) {
	  
    var dataClickable = $(this).parent().attr('data-link');
    if($(this).is(':last-child')){
    }else{
        if(dataClickable && dataClickable.length > 0){
             
            window.location = dataClickable;
          }
    }
        
});

