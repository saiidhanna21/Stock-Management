<?php
function flash($message,$error){
    if($error){
        return
        '<div class="alert alert-danger alert-dismissible fade show">
            '.$message.'
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
    }
    return
    '<div class="alert alert-success alert-dismissible fade show">
        <div class="d-flex align-items-center justify-content-start">
            <span class="alert-icon">
                <i class="anticon anticon-check-o"></i>
            </span>
            <span>'.$message.'</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>';
}
?>
