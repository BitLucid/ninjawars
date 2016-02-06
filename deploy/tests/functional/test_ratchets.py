import os, os.path, fnmatch, re, subprocess


''' Checks the ratchets for the /www/ directory number of scripts,
 and checks the overall SLOC of the project
'''
class TestRatchets:
    ''' Rough file counts in pertinent directories '''
    control_php = 50
    www_php = 10
    plus_minus = 6

    COMMANDS_LINES = 200

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

    ''' Test the lines of code in certain directories, rounded, so that if they change drastically
      we have control of it and notice. '''
    def test_lines_of_code(self):
        '''find /apps/projects/reallycoolapp -type f -iname '*.py' ! -path '*/lib/*' ! -path '*/frameworks/*' | xargs wc -l'''

    ''' Return the lines of code in a directory or file result '''
    def parse_lines(self, path):
        out = subprocess.check_output(['wc', '-l', path])
        matches = re.findall('\d+', str(out))
        return int(next(iter(matches), None))

    ''' The lines in command.php should only decrease '''
    def test_lines_in_command_php(self):
        assert TestRatchets.COMMANDS_LINES-20 < self.parse_lines(self.deploy_dir()+'core/control/commands.php') < TestRatchets.COMMANDS_LINES+20
