<?php

class python_class{

    private string $class_name;
    private array $functions = array();
    private string $module_path;
    private array $return = array();
    private string $interpreter_script_path;    

    /**
     *  @param string $class_name name of the python class
     *  @param string $module_path path of the python file that contains the class
     */
    public function __construct($class_name, $module_path = "modules/system_class.py", $interpreter_script_path = "./py/php_interpreter.py")
    {
        //Check if needed Variables are set
        if(!empty($class_name) || !empty($module_path) || file_exists($interpreter_script_path) || $this->is_py_file($module_path)){
            $this->interpreter_script_path = $interpreter_script_path;
            $this->class_name = $class_name;
            $this->module_path = $this->convert_path_to_py_module($module_path);
        }
    }

    /**
     * resets the functions array and the return array
     */
    public function reset(){
        $this->return = array();
        $this->functions = array();
    }
    
    /**
     * creates a new python class and trys to call all functions saved in functions array
     * @param string $class_name optional name of the class
     * @param string $module optional py module
     * @param array $functions optional array of functions
     * @return array $this->return the response from the python script
     */
    public function execute($module = "", $class_name = "", $functions = ""){
        if($module === ""){
           $module = $this->module_path;
        }
        if($class_name === ""){
            $class_name = $this->class_name;
         }
         if($functions === ""){
            $functions = $this->functions;
         }
        $functions_json = json_encode($functions);
        $command = "python ".$this->interpreter_script_path." ".$class_name." " .$module." ".$functions_json." ";
        $return = array();
        exec($command, $return);
        if($class_name != $this->class_name || $module != $this->module_path) $this->return = $return;
        return $return;
    }

    /**
     * converts path ./x/y/z.a to x.y.z
     */
    private function convert_path_to_py_module($path){
        $module_path = ltrim($path, "py/");
        $module_path = str_replace("/",".", $module_path);
        $module_path = rtrim($module_path, ".py");
        return $module_path;
    }

    /**
     * checks if $path is a python file
     */
    private function is_py_file($path){
        $ret = false;
        if(substr($path, -3) == ".py" && file_exists($path)) $ret = true;
        return $ret;
    }

    public function get_classes_in_module($module_path){
        if($this->is_py_file($module_path)){
            $module = $this->convert_path_to_py_module($module_path);
            $ret = $this->execute("modules.system_class", "System", $this->convert_function(['get_classes_in_module' => ["path"=>$module]]));
            return $ret;
        } else {
            return false;
        }
    }

    public function get_functions_in_class($class_name, $module){
        
    }

    /**
     * adds functions form the function defintion to function_array
     * @param array $function_defintion the definition
     * 
     * array(
     *      'function_name' => array(
     *              "option1" => value1, 
     *                  ...
     *          )
     * )
     */
    public function add_function(array $function_defintion){
        $this->functions = $this->convert_function($function_defintion);
    }

    private function convert_function(array $function_defintion)
    {
        $functions = [];
        foreach($function_defintion as $function => $defintion){
            //Escape " for shell
            $function_name = '"'.$function.'"';
            //Same for options
            $options = array();
            foreach($defintion as $option_key => $option_value){
                $option_key = '"'.$option_key.'"';
                $option_value = '"'.$option_value.'"';
                $options[$option_key] = $option_value;
            }
            $functions[$function_name] = $options;
        }
        return $functions;
    }

    /**
     * removes functions from $function_names from $functions
     * @param array $function_names the names of the functions to remove
     * 
     * array(
     *      'function_name1', 'function_name2','function_name3'
     * )
     */
    public function remove_function(array $function_names){
        foreach($function_names as $function_name){
            unset($this->functions[$function_name]);
        }
    }

    public function get_return(){
        return $this->return;
    }
}

$hallo = new python_class("Test_Class", "py/modules/test_class.py");
$hallo->add_function(array("test_function"=>array(
    "test_key"=>"test_value",
)));

print_r($hallo->get_classes_in_module("py/modules/test_class.py"));
print_r($hallo->execute());
