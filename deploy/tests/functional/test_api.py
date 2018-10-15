import requests
import configparser
import json
import pprint
#from lxml.html import fromstring

CONFIG_PATH = r'./CONFIG'

with open(CONFIG_PATH, 'r') as f:
    config_string = '[nw-config]\n' + f.read()
config = configparser.ConfigParser()
config.read_string(config_string)


class TestApi:
    ''' Hits the api endpoints while logged out, and checks whether anything 
        like json comes back.
    '''

    def root(self):
        '''Get the root url from the config'''
        return config.get('nw-config', 'DOMAIN')

    def status_code(self, url):
        ''' Gets http status codes of pages/urls '''
        try:
            r = requests.head(url, verify=False)
            return r.status_code
        except requests.ConnectionError:
            return None

    def pull_json(self, url, endpoint):
        ''' Get a page to parse as json, for the api 
            there may be better ways to do this later'''
        params = dict(
            type=endpoint,
            jsoncallback='fake'
            )
        resp = requests.get(url=url, params=params, verify=False)
        # strip off the jsonp wrapper
        cut = resp.text[5:-1]
        #data = json.loads(cut)
        # or just resp.json() depending on the version
        return cut


    def test_root_url_config_works(self):
        ''' Ensure root is configured '''
        assert (self.root() is not None and
                len(str(self.root())) > 5)


    def test_api_urls(self):
        root = self.root()
        #endpoints = ['player', 'latest_event', 'chats', 'latest_message',
                #'index', 'latest_chat_id', 'inventory', 'new_chats', 
                #'send_chat', 'char_search']
        endpoints = ['player', 'latest_event', 'chats', 'latest_message',
                'index', 'latest_chat_id', 'new_chats']
        player_data = self.pull_json(root+'/api', 'player') 
        assert (player_data is not None)
        for endpoint in endpoints:
            data = self.pull_json(root+'/api', endpoint)
            assert (data is not None and 
                    json.loads(data) is not False and
                    len(json.loads(data)) > 0
                    )
