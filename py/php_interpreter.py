from pprint import pprint
import sys
import json
import importlib


def load_class(module_path, class_name):
    module = importlib.import_module(module_path)
    myClass = getattr(module, class_name)()
    return myClass

def call_func(myClass, function_name, options):
    function_name = f"do_{function_name}"
    if hasattr(myClass, function_name) and callable(getattr(myClass, function_name)):
        func = getattr(myClass, function_name)
        return func(options)
    else:
        return "NULL"


try:
    if(sys.argv[1] and sys.argv[2] and sys.argv[3]):
        class_name = sys.argv[1]
        module_path = sys.argv[2]
        class_functions = json.loads(sys.argv[3])
        myClass = load_class(module_path, class_name)
        for function in class_functions:
            options = class_functions[function]
            ret = call_func(myClass, function, options)
            print('{"'+function+'": "'+str(ret)+'"}')
    else:
        raise Exception('Not enugth arguments')
except Exception as e:
    print("false")
    print(e)
