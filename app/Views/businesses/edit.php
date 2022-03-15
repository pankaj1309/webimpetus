<?php require_once (APPPATH.'Views/common/edit-title.php'); ?>
                       
<div class="white_card_body">
    <div class="card-body">
        
        <form id="adddomain" method="post" action=<?php echo "/".$tableName."/update";?> enctype="multipart/form-data">
            <div class="form-row">
            
               
                <input type="hidden" class="form-control" name="id" placeholder="" value="<?=@$businesse->id ?>" />

                <div class=" col-md-6">
                    <div class="form-group required col-md-12">
                        <label for="inputEmail4">Name</label>
                        <input type="text" class="form-control required" id="title" name="name" placeholder=""  value="<?=@$businesse->name?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Email</label>
                        <input type="text" class="form-control " id="title" name="email" placeholder=""  value="<?=@$businesse->email?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Company Address</label>
                        <input type="text" class="form-control " id="title" name="company_address" placeholder=""  value="<?=@$businesse->company_address?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Company Number</label>
                        <input type="text" class="form-control " id="title" name="company_number" placeholder=""  value="<?=@$businesse->company_number?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Vat Number</label>
                        <input type="text" class="form-control " id="title" name="vat_number" placeholder=""  value="<?=@$businesse->vat_number?>">
                    </div>
                   
                    
                    
                </div>

                <div class=" col-md-6">
                   
                   
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Web Site</label>
                        <input type="text" class="form-control " id="title" name="web_site" placeholder=""  value="<?=@$businesse->web_site?>">
                    </div>
                   
                  
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Payment Page Url</label>
                        <input type="text" class="form-control " id="payment_page_url" name="payment_page_url" placeholder=""  value="<?=@$businesse->payment_page_url?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Country Code</label>
                        <input type="text" class="form-control " id="country_code" name="country_code" placeholder=""  value="<?=@$businesse->country_code?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Telephone No</label>
                        <input type="text" class="form-control " id="telephone_no" name="telephone_no" placeholder=""  value="<?=@$businesse->telephone_no?>">
                    </div>
                    <div class="form-group col-md-12">
                   <br><span class="help-block">Default Business</span><br>
                        <span class="help-block">
                            <input type="checkbox" name="default_business" id="default_business" <?= !empty(@$businesse->default_business)?"checked":'';?>>
                        </span>
                    </div>
                    
                </div>

                
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

     
<?php require_once (APPPATH.'Views/common/footer.php'); ?>

<script>

    $("#status").on("change", function(){
        var vall = '<?=base64_encode(@$secret->key_value)?>';
        if($(this).is(":checked")===true){
            $('#key_value').val(atob(vall))
        }else{
            $('#key_value').val("*************")
        }
        //alert($(this).is(":checked"))
    })
</script>
