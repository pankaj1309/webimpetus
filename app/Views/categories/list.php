 <?php require_once (APPPATH.'Views/common/list-title.php'); ?>
    <!-- start section for body -->
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example"  class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    
                    <th scope="col">Id</th>
                    <th scope="col">UUID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category Image</th>
                    <th scope="col">Note</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>                                        
            
            <?php foreach($categories as $row):?>
            <tr data-link="categories/edit/<?= $row['id'];?>">
                
                <td class="f_s_12 f_w_400"><?= $row['id'];?>
                </td>
                <td class="f_s_12 f_w_400"><?= $row['uuid'];?> 
                </td>
                <td class="f_s_12 f_w_400  "><?= $row['name'];?>
                </td>
                <td class="f_s_12 f_w_400  ">
                <?php if(!empty($row['image_logo'])) { ?>
                    <img src="<?= $row['image_logo']?>" width="200px">
                <?php } ?>
                
                </td>
                <td class="f_s_12 f_w_400  ">
                        <p class="pd10"> <?= $row['notes'];?></p>
                </td>
                <td class="f_s_12 f_w_400 text-right">
                    <div class="header_more_tool">
                        <div class="dropdown">
                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                <i class="ti-more-alt"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                
                                <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/categories/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                <a class="dropdown-item" href="/categories/edit/<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>

                            </div>
                        </div>
                    </div>
                </td>   
                                                    
            </tr>
            
            <?php endforeach;?>  

            </tbody>
        </table>
    </div>
</div>
  <!-- end section for body -->
<?php require_once (APPPATH.'Views/common/footer.php'); ?>