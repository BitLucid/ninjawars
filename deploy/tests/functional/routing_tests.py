from nose2.tools import *
import requests

ROOT_URL = "http://nw.local/"

def status_code(url):
    try:
        r = requests.head(url)
        return r.status_code
    except requests.ConnectionError:
        return None


def test_basic():
    res = status_code("http://stackoverflow.com");
    print(res == 200)



def test_routing():
    urls = []

def test_urls_should_200():
    urls = ['', 'shop', 'clan', 'shop.php', 'clan.php', 'shop/', 'shop/index', 'shop/buy', 'work.php', 'list.php', 'map.php'];
    [print(200 == status_code(ROOT_URL+url)) for url in urls]

def test_urls_should_404():
    urls = ['thisshould404', 'shoppinginthesudan', 'js/doesnotexist.js', 'shop/willneverexist', 'shopbobby\'-tables']
    [print(404 == status_code(ROOT_URL+url)) for url in urls]

def run_all_tests():
    print('Starting routing tests, the ROOT_URL is'+ROOT_URL)
    test_basic()
    test_urls_should_200()
    test_urls_should_404()


run_all_tests()
