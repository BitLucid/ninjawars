from nose2.tools import *
import requests

ROOT_URL = "http://nw.local/"

def assert_true(outcome, fail_message=''):
    if outcome == True:
        print('.', end='')
    else:
        print(fail_message)

def status_code(url):
    try:
        r = requests.head(url)
        return r.status_code
    except requests.ConnectionError:
        return None


def test_basic():
    res = status_code("http://stackoverflow.com");
    assert_true(res == 200)



def test_routing():
    urls = []

def test_urls_should_200():
    urls = ['', 'staff.php', 'events.php', 'skills.php', 'inventory.php', 'enemies.php', 'list.php', 'clan.php', 'map.php', 'shop.php', 'work.php', 'doshin_office.php', 'clan.php?command=list', 'shop', 'clan', 'shop/', 'shop/index', 'shop/buy'];
    [assert_true(200 == status_code(ROOT_URL+url), 'Url did not 200: ['+url+']') for url in urls]

def test_urls_should_404():
    urls = ['thisshould404', 'shoppinginthesudan', 'js/doesnotexist.js', 'shop/willneverexist', 'shopbobby\'-tables']
    [assert_true(404 == status_code(ROOT_URL+url), 'Url did not 404: ['+url+']') for url in urls]

def run_all_tests():
    print('Starting routing tests, the ROOT_URL is'+ROOT_URL)
    test_basic()
    test_urls_should_200()
    test_urls_should_404()


run_all_tests()
