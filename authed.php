<?php

    $location = 'http://' . str_replace(basename(__DIR__), '', $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']));

    include( 'include/default.php' );

    if ( isset( $_GET[ 'code' ] ) ) :
        $token = GitHubAPI::getAccessToken( $_GET[ 'code' ] );
        $repoName = $_GET['name'];
        $repoDescription = $_GET['desc'];

        if ( $token ) :
            $repo = GitHubAPI::createRepository( $token, $repoName, $repoDescription );

            $hook = GitHubAPI::addHook( $token, $repo );

            $systemCall = 'git clone ' . $repo->git_url . ' ' . __DIR__ . '/../repos/' . $repo->name . '/';

            exec( escapeshellcmd($systemCall), $output );
            Log::Text( 'Repository "' . $repo->name . '" created and cloned', 'AutoDeploy' );
       else :
            Log::Text( 'Failed to get access token for "' . $repoName  .'"', 'AutoDeploy' );
        endif;
    endif;

    header('Location: ' . $location);
?>