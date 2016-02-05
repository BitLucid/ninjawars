#from nose2.tools import *
import requests

''' Handles the routing tests and assertion as well as the global pass/fail state 
 the methods with test_ in their names will be run automatically.
'''
class TestRouting:

    '''Hack to get the root url initially'''
    def root(self):
        return 'http://nw.local/'

    def status_code(self, url):
        try:
            r = requests.head(url)
            return r.status_code
        except requests.ConnectionError:
            return None


    def test_basic(self):
        res = self.status_code("http://stackoverflow.com");
        assert res == 200

    def test_root_url_works(self):
        assert self.root() is not None and len(str(self.root())) > 5


    def test_urls_should_200(self):
        urls = [
            '', 'intro', 'staff.php', 'events.php', 'skills.php', 'inventory.php', 'enemies.php', 'list.php', 
            'clan.php', 'map.php', 'shop.php', 'work.php', 'doshin_office.php', 'dojo.php', 'shrine.php',
            'duel.php', 'clan.php?command=list', 'shop', 'clan', 'shop/', 'shop/index', 'shop/buy',
            'clan.php?command=view', 'npc', 'npc/attack/peasant/', 'npc/attack/guard/',
            ];
        for url in urls:
            assert (str(self.root())+url is not None and 200 == self.status_code(str(self.root())+url))

    def test_urls_that_should_redirect(self):
        urls = [
            'main.php', 'tutorial.php', 'npc.php', 'list_all_players.php', 'webgame/'
            ];
        for url in urls:
            full_uri = str(self.root())+url
            assert str(self.root())+url is not None 
            assert isinstance(self.status_code(full_uri), int)
            assert 301 == self.status_code(str(self.root())+url) or 302 == self.status_code(str(self.root())+url)

    def test_urls_should_404(self):
        urls = ['thisshould404', 'shoppinginthesudan', 'js/doesnotexist.js', 'shop/willneverexist', 'shopbobby\'-tables']
        for url in urls:
            assert (404 == self.status_code(str(self.root())+url))