import requests
from lxml.html import fromstring

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

    def page_title(self, url):
        try:
            r = requests.get(url)
            tree = fromstring(r.content)
            return tree.findtext('.//title')
        except requests.ConnectionError:
            return None


    def test_basic(self):
        res = self.status_code("http://stackoverflow.com");
        assert res == 200

    def test_root_url_config_works(self):
        assert self.root() is not None and len(str(self.root())) > 5

    def test_root_url_loads(self):
        assert self.root() and (200 == self.status_code(self.root()))

    def test_root_url_has_right_title(self):
        assert self.page_title(self.root()) == 'Live by the Shuriken - The Ninja Wars Ninja Game'

    def test_root_url_has_right_title_without_trailing_slash(self):
        url = self.root() 
        assert self.page_title(url[:-1]) == 'Live by the Shuriken - The Ninja Wars Ninja Game'

    def test_urls_should_200(self):
        urls = [
            'intro', 'staff.php', 'events.php', 'skills.php', 'inventory.php', 'enemies.php', 'list.php', 
            'clan.php', 'map.php', 'shop.php', 'work.php', 'doshin_office.php', 'dojo.php', 'shrine.php',
            'duel.php', 'clan.php?command=list', 'shop', 'clan', 'shop/', 'shop/index', 'shop/buy',
            'clan.php?command=view', 'npc', 'npc/attack/peasant/', 'npc/attack/guard/',
            'stats.php', 'account.php',
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
            assert url and (301 == self.status_code(str(self.root())+url) or 302 == self.status_code(str(self.root())+url))

    def test_urls_should_404(self):
        urls = ['thisshould404', 'shoppinginthesudan', 'js/doesnotexist.js', 'shop/willneverexist', 'shopbobby\'-tables']
        for url in urls:
            assert (404 == self.status_code(str(self.root())+url))

    def test_urls_by_title(self):
        root = self.root()
        assert root is not None
        pages = {'signup':'Become a Ninja', 'login':'Login', 
        "clan":"Clan List", "list":"Ninja List", 
        'map.php':'Map', 'staff.php':'Staff', 'village.php':'Chat', 'enemies.php':'Fight',
        'shop.php':'Shop', 'work.php':'Work', 'doshin_office.php':'Doshin Office',
        }
        for url,title in pages.items():
            assert bool(title) and bool(url) and title in self.page_title(root+url)