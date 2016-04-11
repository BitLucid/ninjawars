import os
import os.path
import fnmatch
import re
import subprocess


class TestRatchets:
    ''' Checks the ratchets for the /www/ directory number of scripts,
     and checks the overall SLOC of the project
    '''
    control_php = 37
    www_php = 1
    plus_minus = 6
    www_plus_minus = 1
    ''' Rough file counts in pertinent directories '''

    def deploy_dir(self):
        '''Hack to obtain the web directory path for now '''
        dirname, filename = os.path.split(os.path.abspath(__file__))
        return os.path.realpath(dirname + '/../../') + '/'

    def www_dir(self):
        ''' The publically viewable web directory, these will be conf later'''
        return self.deploy_dir() + 'www/'

    def count_php_in_dir(self, dir):
        ''' Only php extension files in a dir '''
        return len(fnmatch.filter(os.listdir(dir), '*.php'))

    def test_public_dir_files(self):
        ''' Check that the ratchets for public directories match +- '''
        www_dir = self.www_dir()
        assert (www_dir and
                0 < self.count_php_in_dir(www_dir))
        assert www_dir and (
            TestRatchets.www_php - TestRatchets.www_plus_minus <
            self.count_php_in_dir(www_dir) and
            TestRatchets.www_php + TestRatchets.www_plus_minus >
            self.count_php_in_dir(www_dir)
        )

    def test_internal_dir_files(self):
        ''' Check that the ratchets for certain directories match +- '''
        control_dir = self.deploy_dir() + 'lib/control/'
        assert (control_dir and
                0 < self.count_php_in_dir(control_dir))
        assert control_dir
        assert control_dir and (
            TestRatchets.control_php - TestRatchets.plus_minus <
            self.count_php_in_dir(control_dir) and
            TestRatchets.control_php + TestRatchets.plus_minus >
            self.count_php_in_dir(control_dir)
        )

    def parse_lines(self, path):
        ''' Return the lines of code in a directory or file result '''
        out = subprocess.check_output(['wc', '-l', path])
        matches = re.findall('\d+', str(out))
        return int(next(iter(matches), None))

'''
    def test_lines_of_code(self):
 Test the lines of code in certain directories,
            rounded LOC, so that if they change drastically!
            we have control of it and notice.

        # Stub: Use something like this shell command:
        #find /apps/projects/reallycoolapp -type f -iname '*.py' !
        # -path '*/lib/*' ! -path '*/frameworks/*' | xargs wc -l
'''
