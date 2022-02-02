<?php require_once (APPPATH.'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example"  class="table tableDocument table-bordered table-hover">
            <thead>
                <tr>
                    <!--th scope="col">
                        <input type="checkbox" class="check_all" onclick="set_check_all(this);">
                    </th-->
                    <th scope="col">Id</th>
                    <th scope="col">UUID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Address</th>
                    
                    <th scope="col">Note</th>
                    <th scope="col">Status</th><th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>                                        
            
            <?php foreach($users as $row):?>
            
            <tr data-href="/users/edit/<?= $row['id'];?>">
                
                <!--td class="checkDocument">
                    <input type="checkbox" class="check_all" onclick="set_check_all(this);">
                </td-->
                <td class="f_s_12 f_w_400"><a href="/users/edit/<?= $row['id'];?>"><?= $row['id'];?></a></td>
                <td class="f_s_12 f_w_400"><a href="/users/edit/<?= $row['id'];?>"><?= $row['uuid'];?> </a></td>
                <td class="f_s_12 f_w_400 text_color_1 "><a href="/users/edit/<?= $row['id'];?>"><?= $row['name'];?></a></td>
                    <td class="f_s_12 f_w_400  "><a href="/users/edit/<?= $row['id'];?>"><?= $row['email'];?></a></td>
                    <td class="f_s_12 f_w_400  "><a href="/users/edit/<?= $row['id'];?>"><?= $row['address'];?></a></td>
                    
                    <td class="f_s_12 f_w_400 text_color_1 ">
                        <p class="pd10"> <?= $row['notes'];?></p>
                    </td>
                <td class="f_s_12 f_w_400  ">
                        <div class="">
                        <label class="switch2">
                            <input type="checkbox" class="checkb" data-url="users/status" name="checkb[]" value="<?= $row['id'];?>" <?=($row['status']==1)?'checked':''?>  />
                            <span class="slider round"></span>
                        </label>
                    </div>
                    </td> <td class="f_s_12 f_w_400 text-right">
                    <div class="header_more_tool">
                        <div class="dropdown">
                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                <i class="ti-more-alt"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                
                                <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/users/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                <a class="dropdown-item" href="/users/edit/<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>

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

<?php require_once (APPPATH.'Views/common/footer.php'); ?>