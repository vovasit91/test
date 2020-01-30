<?php


namespace App;


class Serializer
{
    public static function objectToArray(object $object)
    {
        try{
            $name = '\stdClass';
            $isStdClass = $object instanceof $name;
            $reflection = new \ReflectionClass($object);
            $props = [];
            foreach ($isStdClass ? get_object_vars($object) : $reflection->getProperties() as $key => $prop) {
                if($isStdClass){
                    $props[$key] = $prop;
                }
                elseif($prop->isPublic() || $prop->isProtected()){
                    $prop->setAccessible(true);
                    $props[$prop->getName()] = $prop->getValue($object);
                }
            }
        } catch (\ReflectionException $exception) {}

        return isset($props) ? $props : false;
    }

    /**
     * @param $path_to_file
     * @return mixed|string
     * @credits http://jarretbyrne.com/2015/06/197/
     */
    public static function extractClassName($path_to_file)
    {
        //Grab the contents of the file
        $contents = file_get_contents($path_to_file);

        //Start with a blank namespace and class
        $namespace = $class = "";

        //Set helper values to know that we have found the namespace/class token and need to collect the string values after them
        $getting_namespace = $getting_class = false;

        //Go through each token and evaluate it as necessary
        foreach (token_get_all($contents) as $token) {

            //If this token is the namespace declaring, then flag that the next tokens will be the namespace name
            if (is_array($token) && $token[0] == T_NAMESPACE) {
                $getting_namespace = true;
            }

            //If this token is the class declaring, then flag that the next tokens will be the class name
            if (is_array($token) && $token[0] == T_CLASS) {
                $getting_class = true;
            }

            //While we're grabbing the namespace name...
            if ($getting_namespace === true) {

                //If the token is a string or the namespace separator...
                if(is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {

                    //Append the token's value to the name of the namespace
                    $namespace .= $token[1];

                }
                else if ($token === ';') {

                    //If the token is the semicolon, then we're done with the namespace declaration
                    $getting_namespace = false;

                }
            }

            //While we're grabbing the class name...
            if ($getting_class === true) {

                //If the token is a string, it's the name of the class
                if(is_array($token) && $token[0] == T_STRING) {

                    //Store the token's value as the class name
                    $class = $token[1];

                    //Got what we need, stope here
                    break;
                }
            }
        }

        //Build the fully-qualified class name and return it
        return $namespace ? $namespace . '\\' . $class : $class;

    }
}