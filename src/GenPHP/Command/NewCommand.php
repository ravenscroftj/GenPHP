<?php 
namespace GenPHP\Command;
use CLIFramework\Command;
use GetOptionKit\GetOptionKit;
use GetOptionKit\OptionParser;
use GetOptionKit\OptionResult;
use GetOptionKit\OptionSpecCollection;
use GenPHP\Flavor;
use Exception;
use ReflectionObject;

class NewCommand extends Command
{

    function brief() { return 'generate from flavor'; }

    function execute($flavorName)
    {
        $logger = $this->getLogger();
        $formatter = $logger->getFormatter();
        $specs = new OptionSpecCollection;

        /* load flavor generator */
        $logger->info("Loading flavor $flavorName...");
        $loader = new Flavor\FlavorLoader;
        $flavor = $loader->load( $flavorName );
        $generator = $flavor->getGenerator();

        $logger->info2("Inializing option specs...");
        $generator->options( $specs );
        $generator->setLogger( $this->getLogger() );

        $deps = $generator->getDependencies();

        /*
        if( count($deps) )
            $logger->info("Dependencies: " . 
                    join(' ',array_keys($deps)) );
        */

        foreach( $deps as $depGenerator ) {
            $depGenerator->logAction( "dependency", get_class($depGenerator) , 1 );
            $args = $depGenerator->getOption()->getArguments();
            $this->runGenerator( $depGenerator , $args );
        }

        /* use GetOptionKit to parse options from $args */
        $args = func_get_args();
        array_shift($args);
        $parser = new OptionParser( $specs );
        $result = $parser->parse( $args );

        /* pass rest arguments for generation */
        $generator->setOption( $result );

        $logger->info("Running main generator...");
        $this->runGenerator( $generator , $result->getArguments() );
        $logger->info("Done");
    }

    public function checkGeneratorParameters($generator,$args)
    {
        $gClass = get_class( $generator );
        $refl = new ReflectionObject($generator);
        $reflMethod = $refl->getMethod('generate');
        $requiredNumber = $reflMethod->getNumberOfRequiredParameters();
        if( count($args) < $requiredNumber ) {
            $this->getLogger()->error( "Generator $gClass requires $requiredNumber arguments." );
            $params = $reflMethod->getParameters();
            foreach( $params as $param ) {
                $this->getLogger()->error( 
                    $param->getPosition() . ' => $' . $param->getName() , 1 );
            }
            throw new Exception;
        }
    }

    public function runGenerator($generator,$args = array()) 
    {
        $this->checkGeneratorParameters($generator,$args);
        return call_user_func_array( array($generator,'generate'),$args);
    }

}
