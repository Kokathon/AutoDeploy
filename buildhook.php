<?php
    include( 'include/default.php' );

    if ( isset( $_POST[ 'payload' ] ) ) :
        $data = json_decode( $_POST[ 'payload' ] );
        $repository = $data->repository;
        $systemCall = 'git --git-dir=' . __DIR__ . '/../repos/' . $repository->name . '/.git --work-tree=' . __DIR__ . '/../repos/' . $repository->name . ' pull';

        exec( escapeshellcmd($systemCall), $output );

        //Log::text($_POST['payload']);

        foreach ($data->commits as $commit) {
        	Log::text( 'Repository updated by ' . $data->pusher->name .': ' . $commit->message, $repository->name );	
        }

        
    endif;
?>