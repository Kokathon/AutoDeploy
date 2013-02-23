<?php
    include( 'include/default.php' );

    if ( isset( $_GET[ 'code' ] ) ) :
        $token = GitHubAPI::getAccessToken( $_GET[ 'code' ] );
        $repoName = $_GET['name'];
        $repoDescription = $_GET['desc'];

        if ( $token ) :
            $repo = GitHubAPI::createRepository( $token, $repoName, $repoDescription );

            $hook = GitHubAPI::addHook( $token, $repo );

            $systemCall = 'git clone ' . $repo->git_url . ' ' . __DIR__ . '/../repos/' . $repo->name . '/';

            exec( $systemCall, $output );
            Log::Text( 'Repository "' . $repo->name . '" created and cloned', 'AutoDeploy' );


            header('Location: http://kokarn.com/kokathon/');

       else :
            Log::Text( 'Failed to get access token for "' . $repoName  .'"', 'AutoDeploy' );
            header('Location: http://kokarn.com/kokathon/');
        endif;
    endif;
?>