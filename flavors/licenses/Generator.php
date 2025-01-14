<?php
namespace licenses;
use GenPHP\Flavor\BaseGenerator;

class Generator extends BaseGenerator 
{

    function brief()
    {
        return 'brief for your generator';
    }

    function dependency()
    {
        /* describe dependency here */
        return array( 
            'flavor2' => array( 
                'options' => array( 'path' => '....' ),
                'arguments' => array( 'arg1' , 'arg2' ),
            ),
        );
    }


    function generate(/* $arg1, $arg2,  */) 
    {
        /* 
            do generate here:

            create dir
                $this->createDir( 'path/to/dir' );

            render code
                $this->render( 'templatePath.php.twig', 'path/to/file' , array( 'name' => $name )  );

            copy directory
                $this->copyDir( 'path/from' , 'path/to' );

            create file
                $this->create( 'path/to/file' , 'content' );

            touch file
                $this->touch( 'path/to/touch' );
         */
    }

}

