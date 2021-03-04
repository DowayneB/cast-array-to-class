<?php

class ArrayThings
{
 public static function castArrayToClass(array $array, string $className, array $args = [], bool $strict = false): object
    {
        if (!class_exists($className)) {
            throw new \Exception('Class does not exist');
        }

        $reflection = new \ReflectionClass($className);

        if ($reflection->getConstructor() instanceof \ReflectionMethod) {
            if (count($reflection->getConstructor()->getParameters()) !== count($args)){
                throw new \Exception('The number of arguments specified does not match that of the provided class');
            }

            $class = call_user_func_array([$reflection,'newInstance'],$args);
        }else{
            $class = (new \ReflectionClass($className))->newInstance();
        }

        foreach ($array as $key => $item) {
            $methodName = 'set'.ucfirst($key);
            if (method_exists($class,$methodName)) {
                $class->$methodName($item);
            }elseif ($strict){
                throw new \Exception(sprintf(
                    'The array key [%s] is not compatible with %s',
                    $key,
                    $className
                ));
            }
        }

        return $class;
    }
}

?>
