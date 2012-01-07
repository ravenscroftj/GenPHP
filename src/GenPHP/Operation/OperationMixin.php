<?php 
namespace GenPHP\Operation;
use Exception;
use GenPHP\Operation\Helper;

/**
 * Deletator for operations 
 */
class OperationMixin 
{
    public $self;

    public function __construct($self)
    {
        $this->self = $self;
    }

    public function __call($method,$args)
    {
        $class = '\\GenPHP\\Operation\\' . ucfirst($method) . 'Operation';
        spl_autoload_call( $class );
        if( class_exists($class) ) {
            $operation = new $class( $this->self );
            call_user_func_array( array($operation,'run') , $args );
        } else {
            throw new Exception("Operation class not found: $class");
        }
    }

}

