import requests
import configparser
from lxml.html import fromstring

CONFIG_PATH = r'./CONFIG'

with open(CONFIG_PATH, 'r') as f:
    config_string = '[nw-config]\n' + f.read()
config = configparser.ConfigParser()
config.read_string(config_string)


class TestRouting:
    ''' Handles the routing tests and assertion
        as well as the global pass/fail state
        the methods with test_ in their names will be run automatically.
    '''

    def root(self):
        '''Hack to get the root url initially'''
        return config.get('nw-config', 'DOMAIN')

    def status_code(self, url):
        ''' Gets http status codes of pages/urls '''
        try:
            r = requests.head(url)
            return r.status_code
        except requests.ConnectionError:
            return None

    def page_title(self, url):
        ''' Get the lexed title from the html '''
        try:
            r = requests.get(url)
            tree = fromstring(r.content)
            return tree.findtext('.//title')
        except requests.ConnectionError:
            return None

    def test_root_url_config_works(self):
        ''' Ensure root is configured '''
        assert (self.root() is not None and
                len(str(self.root())) > 5)

    def test_root_url_loads(self):
        assert (self.root() and
                (200 == self.status_code(self.root())))

    def test_root_url_has_right_title(self):
        title = 'Live by the Shuriken - The Ninja Wars Ninja Game'
        assert (self.page_title(self.root()) == title)

    def test_root_url_has_right_title_without_trailing_slash(self):
        url = self.root()
        title = 'Live by the Shuriken - The Ninja Wars Ninja Game'
        assert (self.page_title(url[:-1]) == title)

    def test_urls_should_200(self):
        urls = [
            'intro', 'login', 'login.php', 'signup', 'signup.php', 
            'player.php', 'village.php', 'interview.php', 'news/', 
            'staff.php', 'list.php', 'rules.php', 'shop.php', 'events.php', 
            'skill', 'inventory.php', 'inventory', 'enemies.php',
            'clan.php', 'map.php', 'work.php', 'doshin_office.php',
            'dojo.php', 'shrine.php', 'duel.php', 'clan.php?command=list',
            'shop', 'clan', 'shop/', 'shop/index', 'shop/buy',
            'clan.php?command=view', 'npc', 'npc/attack/peasant/',
            'npc/attack/guard/', 'stats.php', 'account.php', 'quest',
            'quest/view/1', 'account_issues.php', 'resetpassword.php',
            'player.php?target_id=777777',
            'player.php?target=tchalvak', 'item/self_use/amanita',
            'skill/use/Fire%20Bolt/tchalvak', 'skill/self_use/Heal',
            'item/self_use/3', 'item/self_use/1',
            'item/use/shuriken/tchalvak', 'dojo/buyDimMak',
        ]
        #Eventually some of these urls should be tested on logged in user.
        for url in urls:
            assert (str(self.root()) + url is not None and 200 ==
                    self.status_code(str(self.root()) + url))

    def test_urls_that_should_redirect(self):
        urls = [
            'main.php', 'tutorial.php', 'npc.php', 'list_all_players.php',
            'webgame/', 'ninjamaster', 'ninjamaster/tools',
            'ninjamaster/player_tags'
        ]
        for url in urls:
            full_uri = str(self.root()) + url
            assert str(self.root()) + url is not None
            assert isinstance(self.status_code(full_uri), int)
            assert url and (
                301 == self.status_code(str(self.root()) + url) or
                302 == self.status_code(str(self.root()) + url))

    def test_urls_should_404(self):
        urls = ['thisshould404', 'shoppinginthesudan',
                'js/doesnotexist.js', 'shop/willneverexist',
                'shopbobby\'-tables']
        for url in urls:
            assert (404 == self.status_code(str(self.root()) + url))

    def test_urls_should_500(self):
        urls = ['error']
        for url in urls:
            assert (500 == self.status_code(str(self.root()) + url))

    def test_urls_by_title(self):
        root = self.root()
        assert root is not None
        pages = {'signup': 'Become a Ninja', 'login': 'Login',
                 "clan": "Clan List", "list": "Ninja List",
                 'map.php': 'Map', 'staff.php': 'Staff',
                 'village.php': 'Chat', 'enemies.php': 'Fight',
                 'shop.php': 'Shop', 'work.php': 'Work',
                 'doshin_office.php': 'Doshin Office',
                 'account_issues.php' : 'Account Problems',
                 'resetpassword.php' : 'Request a password reset',
                 'player.php' : 'Ninja Profile',
                 'player.php?target=tchalvak' : 'Ninja: Tchalvak',
                 'npc/attack/peasant' : 'Battle',
                 'news' : 'News Board'
                 }
        for url, title in pages.items():
            assert (bool(title) and bool(url) and
                    None is not self.page_title(root + url))
            assert (bool(title) and bool(url) and
                    title in self.page_title(root + url))
