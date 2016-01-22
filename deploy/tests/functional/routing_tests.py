#from nose2.tools import *
import requests

''' Handles the routing tests and assertion as well as the global pass/fail state '''
class RoutingTests:

    def __init__(self, root):
        self.fail = 'no tests run'
        #Initial state is to fail and return a failing exit code
        self.root = root

    def outcome(self):
        return self.fail

    def assert_true(self, outcome, fail_message=''):
        if outcome == True:
            print('.', end='')
            # Make self.fail valid if tests continue to only pass.
            if self.fail == 'no tests run' or self.fail == 0:
                self.fail = 0

        else:
            print(fail_message)
            self.fail = fail_message

    def status_code(self, url):
        try:
            r = requests.head(url)
            return r.status_code
        except requests.ConnectionError:
            return None


    def test_basic(self):
        res = self.status_code("http://stackoverflow.com");
        self.assert_true(res == 200)


    def test_urls_should_200(self):
        urls = [
            '', 'staff.php', 'events.php', 'skills.php', 'inventory.php', 'enemies.php', 'list.php', 
            'clan.php', 'map.php', 'shop.php', 'work.php', 'doshin_office.php', 'dojo.php', 'shrine.php',
            'duel.php', 'clan.php?command=list', 'shop', 'clan', 'shop/', 'shop/index', 'shop/buy',
            'clan.php?command=view', 'npc', 'npc/attack/peasant/', 'npc/attack/guard/',
            ];
        [self.assert_true(200 == self.status_code(self.root+url), 'Url did not 200: ['+url+']') for url in urls]

    def test_urls_should_404(self):
        urls = ['thisshould404', 'shoppinginthesudan', 'js/doesnotexist.js', 'shop/willneverexist', 'shopbobby\'-tables']
        [self.assert_true(404 == self.status_code(self.root+url), 'Url did not 404: ['+url+']') for url in urls]

    '''Have to manually add put the test functions to be run in here for the moment'''
    def run_all_tests(self):
        print('Starting routing tests, the root url is '+self.root)
        self.test_basic()
        self.test_urls_should_200()
        self.test_urls_should_404()



# Executing the tests section


'''Yep, it's a hack until I figure out how to do configuration in python.'''
routing = RoutingTests('http://nw.local/')

routing.run_all_tests()
print("\nLatest test error was:")
exit(routing.outcome())
