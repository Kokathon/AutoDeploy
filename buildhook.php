<?php
if ( isset( $_POST[ 'payload' ] ) ) :
    $data = json_decode( $_POST[ 'payload' ] );
    $repository = $data->repository;
    system( 'git --git-dir=../' . $repository->name .'/.git --work-tree=../' . $repository->name . ' pull');
endif;
?>