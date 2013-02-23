<?php
    include( 'include/default.php' );

    if ( isset( $_POST[ 'payload' ] ) ) :
        $data = json_decode( $_POST[ 'payload' ] );
        $repository = $data->repository;
        if ( !isset( $_GET[ 'pull' ] ) || $_GET[ 'pull' ] == 'yes' ) :
            $systemCall = 'git --git-dir=../' . $repository->name . '/.git --work-tree=../' . $repository->name . ' pull';
            exec( $systemCall, $output );
        endif;
        Log::text( 'Repository updated by "' . $data->pusher->name . '"', $repository->name );
    endif;
?>