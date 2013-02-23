<?php
    class Log {
        private static $logFile = '/messages.log';
        private static $dateFormat = 'd-M-o H:i:s T';
        private static $message = '';
        private static $namespace = 'Log';
        private static $driver = 'mongodb';

        static function text( $text, $namespace = 'Log' ) {

            if ( $namespace != 'Log' ) :
                self::$namespace = $namespace;
            endif;

            if ( is_array( $text ) ) :
                foreach ( $text as $line ) :
                    self::$message .= self::formatLine( $line );
                endforeach;
            else :
                self::$message = $text; //self::formatLine( $text );
            endif;

            self::writeLine();
        }

        static function formatLine( $line ) {
            //$message = date( self::$dateFormat ) . '|' . $line . '|' . self::$namespace . "\n";
            $message = $line . "\n";

            return $message;
        }

        static function getLogFilePath() {
            return __DIR__ . self::$logFile;
        }

        static function printAsTable() {
            ?>
            <table class="table table-hover table-condensed js-messages-wrapper">
                <thead>
                    <tr>
                        <th>
                            Time
                        </th>
                        <th>
                            Message
                        </th>
                        <th>
                            Project
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $filePath = __DIR__ . self::$logFile;
                    if( !file_exists( $filePath ) ) :
                        ?>
                        <tr>
                            <td colspan="3">
                                No messages yet
                            </td>
                        </tr>
                        <?php
                    else :
                        //$errors = file( __DIR__ . self::$logFile );

                        $client = new MongoClient();
                        $db = $client->log;
                        $collection = $db->messages;

                        $errors = $collection->find()->sort( array(
                            'date' => -1
                        ) )->limit(100);

                        //$collection->drop();

                        //$reverseErrors = array_reverse( $errors );
                        //foreach ( $reverseErrors as $error ) :
                        foreach ( $errors as $error ) :
                            //$parts = explode( '|', $error, 3 );

                            $message = $error[ 'message' ];
                            if( is_array( $error[ 'message' ] ) ) :
                                $message = array_shift( $error[ 'message' ] );
                            endif;
                            ?>
                            <tr>
                                <td>
                                    <?php echo $error[ 'date' ]; ?>
                                </td>
                                <td>
                                    <?php echo $message; ?>
                                </td>
                                <td>
                                    <?php echo $error[ 'project' ]; ?>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        <?php
        }

        static function getLastLine(){
            $client = new MongoClient();
            $db = $client->log;
            $collection = $db->messages;

            $errors = $collection->find()->sort( array(
                'date' => -1
            ) )->limit(1);

            foreach ( $errors as $error ) :
                $message = $error[ 'message' ];
                if( is_array( $error[ 'message' ] ) ) :
                    $message = array_shift( $error[ 'message' ] );
                endif;
                ?>
                <tr>
                    <td>
                        <?php echo $error[ 'date' ]; ?>
                    </td>
                    <td>
                        <?php echo $message; ?>
                    </td>
                    <td>
                        <?php echo $error[ 'project' ]; ?>
                    </td>
                </tr>
            <?php
            endforeach;
        }

        static function writeLine() {
            switch( self::$driver ) {
                case 'mongodb':
                    $client = new MongoClient();
                    $db = $client->log;

                    $collection = $db->messages;

                    $collection->insert( array(
                        'date' => date( self::$dateFormat ),
                        'message' => self::$message,
                        'project' => self::$namespace
                    ) );

                    break;
                default:
                    $fp = fopen( __DIR__ . self::$logFile, 'a' );
                    fwrite( $fp, self::$message );
                    fclose( $fp );

                    break;
            }
        }
    }

?>