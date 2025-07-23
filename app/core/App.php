<?php

namespace AppDAF\CORE;

use AppDAF\ENUM\ClassName;
use Symfony\Component\Yaml\Yaml;

class App
{

    private static array $dependencies;

    public static function getDependencie(ClassName $className): mixed
    {
        self::$dependencies = Yaml::parseFile(SERVICES_PATH);

        if (!array_key_exists($className->value, self::$dependencies)) {
            throw new \Exception("La dépendance {$className->value} est introuvable.");
        }

        $definition = self::$dependencies[$className->value];
        $classNameStr = $definition['class'] ?? null;
        $arguments = $definition['argument'] ?? [];

        if (!$classNameStr || !class_exists($classNameStr)) {
            throw new \Exception("Classe non valide pour le service {$className->value}.");
        }

        try {
            $resolvedArgs = [];

            foreach ($arguments as $argName) {
                $argClassName = ClassName::from($argName); 
                $resolvedArgs[] = self::getDependencie($argClassName);
            }
            
            $reflector = new \ReflectionClass($classNameStr);

            return $reflector->newInstanceArgs($resolvedArgs);
        } catch (\ReflectionException $e) {
            throw new \Exception("Erreur de réflexion : " . $e->getMessage());
        }
    }
}
