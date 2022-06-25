<?php include('common/header.php'); ?>
<!-- main content part here -->
 

<?php include('common/sidebar.php'); ?>

<section class="main_content dashboard_part large_header_bg">
       <?php include('common/top-header.php'); ?> 
  <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left d-flex align-items-center">
                            <h3 class="f_s_25 f_w_700 dark_text mr_30" >Blocks </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Blocks</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                           <a href="/blocks" class="btn btn-primary"><i class="fa fa-table"></i> Blocks List</a>
                        </div>
                      
                    </div>
                </div>
            </div>
            <div class="row ">

                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_30">
                       
                        <div class="white_card_body">
                            <div class="card-body">
                               
                                <form id="adddomain" method="post" action="/blocks/update" enctype="multipart/form-data">
                                   <div class="form-row">
									
									
										
										
                                        <div class="form-group col-md-12">
                                            <label for="inputEmail4">Code</label>
                                            <input type="text" class="form-control" id="title" name="code" placeholder=""  value="<?=$content->code?>">
											<input type="hidden" class="form-control" name="id" placeholder="" value="<?=$content->id ?>" />
                                        </div>

                                          
                                         <div class="form-group col-md-12">
                                            <label for="inputPassword4">Text</label>
                                          <textarea class="form-control" name="text" style="width:100%!important;height:250px" ><?=$content->text?></textarea> 
                                        </div>
                                    
                                   
                                       
<div class="form-group col-md-12">
                                            <label for="inputEmail4">Status</label>
											</div>
											<div class="form-group col-md-12">
											
                                            <label for="inputEmail4"><input type="radio" value="1" class="form-control" id="status" name="status" <?=$content->status==1?'checked':''?> placeholder=""> Yes</label>
											
											 <label for="inputEmail4"><input type="radio" <?=$content->status==0?'checked':''?> value="0" class="form-control" id="status" name="status" placeholder=""> No</label>
                                        </div>
                                      
                                   

                                    
                                    </div>

                                    
                                   


                                    
                                  

                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
              

             
               
            </div>
        </div>
    </div>

     

<?php include('common/footer.php'); ?>
</section>
<!-- main content part end -->

<?php include('common/scripts.php'); ?>

    <script>
		// Add the following code if you want the name of the file appear on select
		$(".custom-file-input").on("change", function() {
		  var fileName = $(this).val().split("\\").pop();
		  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		});
	</script>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
 <script>
   if ($("#adddomain").length > 0) {
      $("#adddomain").validate({
    rules: {
      code: {
        required: true,
      }, 
      text: {
        required: true,
      }  
    },
    messages: {
      code: {
        required: "Please enter code",
      }
        
    },
  })
}
</script>