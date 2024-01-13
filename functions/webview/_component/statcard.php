<?php
function webview___component__statcard($data){
    if(!isset($data['color'])) $data['color'] = 'info';
    if(!isset($data['title'])) $data['title'] = '';
    if(!isset($data['icon'])) $data['icon'] = 'fa-check';
    if(!isset($data['content'])) $data['content'] = '';
    ?>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-<?=$data['color']?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-<?=$data['color']?> text-uppercase mb-3">
                            <?=$data['title']?>
                        </div>
                        <?=$data['content']?>
                    </div>
                    <div class="col-auto">
                        <i class="fas <?=$data['icon']?> fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return true;
}
    