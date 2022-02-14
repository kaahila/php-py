class System:
    def do_get_classes_in_module(self, module):
        return dir(module['path'])


    def do_get_functions_in_class(self, myClass):
        return dir(myClass['name'])