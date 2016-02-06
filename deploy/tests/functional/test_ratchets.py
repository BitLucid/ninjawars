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

    def count_dir_php(self, dir):
        return len(fnmatch.filter(os.listdir(dir), '*.php'))
        #path, dirs, files = next(os.walk(dir))
        #file_count = len(files)
        #return file_count
        #return len([name for name in os.listdir(dir) if os.path.isfile(name)])

    def test_directories_exist(self):
        assert 0 < len(self.www_dir())
        assert 0 < len(self.deploy_dir()+'lib/control/')

    def test_dir_files(self):
        control_dir = self.deploy_dir()+'lib/control/'
        www_dir  = self.www_dir()
        assert control_dir and 0 < self.count_dir_php(control_dir)
        assert www_dir and 0 < self.count_dir_php(www_dir)
        assert control_dir 
        assert control_dir and (
            TestRatchets.control_php-TestRatchets.plus_minus < self.count_dir_php(control_dir) and
            TestRatchets.control_php+TestRatchets.plus_minus > self.count_dir_php(control_dir)
            )
        assert www_dir and (
            TestRatchets.www_php-TestRatchets.plus_minus < self.count_dir_php(www_dir) and
            TestRatchets.www_php+TestRatchets.plus_minus > self.count_dir_php(www_dir)
            )


'''
t = TestRatchets();
t.test_directories_exist()
t.test_dir_files()
print(t.deploy_dir())
print(t.www_dir())

# simple version for working with CWD
print(t.count_directory_files(t.www_dir()))
print([name for name in os.listdir('.') if os.path.isfile(name)])

# path joining version for other paths
DIR = '/tmp'
print(len([name for name in os.listdir(DIR) if os.path.isfile(os.path.join(DIR, name))]))

dirname, filename = os.path.split(os.path.abspath(__file__))
print("running from", dirname)
print("running towards", )
print("file is", filename)
'''