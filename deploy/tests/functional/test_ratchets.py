import os, os.path, fnmatch


''' Checks the ratchets for the /www/ directory number of scripts,
 and checks the overall SLOC of the project
'''
class TestRatchets:
    ''' Rough file counts in pertinent directories '''
    control_php = 50
    www_php = 10
    plus_minus = 6

    '''Hack to obtain the web directory path'''
    def deploy_dir(self):
        dirname, filename = os.path.split(os.path.abspath(__file__))
        return os.path.realpath(dirname+'/../../')+'/'

    def www_dir(self):
        return self.deploy_dir()+'www/'

    def count_php_in_dir(self, dir):
        return len(fnmatch.filter(os.listdir(dir), '*.php'))

    def test_directories_exist(self):
        assert 0 < len(self.www_dir())
        assert 0 < len(self.deploy_dir()+'lib/control/')

    def test_dir_files(self):
        control_dir = self.deploy_dir()+'lib/control/'
        www_dir  = self.www_dir()
        assert control_dir and 0 < self.count_php_in_dir(control_dir)
        assert www_dir and 0 < self.count_php_in_dir(www_dir)
        assert control_dir 
        assert control_dir and (
            TestRatchets.control_php-TestRatchets.plus_minus < self.count_php_in_dir(control_dir) and
            TestRatchets.control_php+TestRatchets.plus_minus > self.count_php_in_dir(control_dir)
            )
        assert www_dir and (
            TestRatchets.www_php-TestRatchets.plus_minus < self.count_php_in_dir(www_dir) and
            TestRatchets.www_php+TestRatchets.plus_minus > self.count_php_in_dir(www_dir)
            )
