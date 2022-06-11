 <?php require_once (APPPATH.'Views/media_list/list-title.php'); ?>
 <div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example"  class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>

                    <th scope="col">Id</th>
                    <th scope="col">Code</th>
                    <th scope="col">Image</th>
                    <th scope="col">status</th>

                    <th scope="col">created at</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>                                        

                <?php foreach($media_list as $row):?>
                    <tr data-link="/gallery/edit/<?= $row['id'];?>">

                        <td class="f_s_12 f_w_400"><?= $row['id'];?></td>
                        <td class="f_s_12 f_w_400"><?= $row['code'];?>
                        <td class="f_s_12 f_w_400"><?php if(!empty($row['name'])) {
                            echo render_image($row['name']); 
                         } ?>
                        </td>
                        <td class="f_s_12 f_w_400 <?=$row['status']==0?'text_color_1':'text_color_2'?> ">
                            <?=$row['status']==0?'inactive':'active'?>
                        </td>


                        <td class="f_s_12 f_w_400  ">
                            <p class="pd10"> <?= $row['created'];?></p>
                        </td>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/gallery/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="/gallery/edit/<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>


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
