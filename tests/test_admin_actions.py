"""Exercise the real admin forms, auth guard and handlers with database doubles."""
import http.cookiejar
from html.parser import HTMLParser
import json
import os
from pathlib import Path
import shutil
import socket
import subprocess
import tempfile
import time
import unittest
import urllib.error
import urllib.parse
import urllib.request


REPO = Path(__file__).resolve().parents[1]
PHP = os.environ.get('ADMIN_TEST_PHP', 'php')


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, request, fp, code, message, headers, url):
        return None


class Forms(HTMLParser):
    def __init__(self):
        super().__init__()
        self.forms = []
        self.current = None

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == 'form':
            self.current = {'action': attrs.get('action'), 'method': attrs.get('method'), 'data': {}}
        elif tag == 'input' and self.current is not None and 'name' in attrs:
            self.current['data'][attrs['name']] = attrs.get('value', '')

    def handle_endtag(self, tag):
        if tag == 'form' and self.current is not None:
            self.forms.append(self.current)
            self.current = None


class AdminActionTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.temporary = tempfile.TemporaryDirectory(prefix='admin-action-test-')
        cls.root = Path(cls.temporary.name).resolve()
        paths = ['files/include/auth.php', 'files/include/admin-action.php', 'files/include/nav.php',
                 'khuljasimsim/user/index.php', 'khuljasimsim/user/changeStatus.php',
                 'khuljasimsim/property/index.php', 'khuljasimsim/property/remove.php']
        for name in paths:
            target = cls.root / name
            target.parent.mkdir(parents=True, exist_ok=True)
            shutil.copyfile(REPO / name, target)
        bootstrap = cls.root / 'api/autoload/init.php'
        bootstrap.parent.mkdir(parents=True, exist_ok=True)
        shutil.copyfile(REPO / 'tests/fixtures/admin_bootstrap.php', bootstrap)
        shutil.copyfile(REPO / 'tests/fixtures/admin_router.php', cls.root / 'router.php')
        (cls.root / 'sessions').mkdir()
        (cls.root / 'state.json').write_text('[]')
        with socket.socket() as listener:
            listener.bind(('127.0.0.1', 0))
            port = listener.getsockname()[1]
        cls.base = f'http://127.0.0.1:{port}'
        cls.log = (cls.root / 'server.log').open('w+b')
        cls.server = subprocess.Popen([PHP, '-n', '-d', f'session.save_path={cls.root / "sessions"}',
                                       '-d', 'session.cookie_samesite=Lax', '-S', f'127.0.0.1:{port}',
                                       str(cls.root / 'router.php')], cwd=cls.root, stdout=cls.log, stderr=cls.log,
                                      creationflags=subprocess.CREATE_NO_WINDOW if os.name == 'nt' else 0)
        cls.addClassCleanup(cls.stop_server)
        for _ in range(50):
            try:
                with urllib.request.urlopen(cls.base + '/__test/ready', timeout=1):
                    return
            except (OSError, urllib.error.URLError):
                if cls.server.poll() is not None:
                    break
                time.sleep(0.1)
        cls.log.seek(0)
        raise RuntimeError(cls.log.read().decode(errors='replace'))

    @classmethod
    def stop_server(cls):
        cls.server.terminate()
        cls.server.wait(timeout=5)
        cls.log.close()
        assert cls.root == Path(cls.temporary.name).resolve()
        cls.temporary.cleanup()

    def setUp(self):
        self.client = self.new_client()
        (self.root / 'state.json').write_text('[]')

    @staticmethod
    def new_client():
        return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), NoRedirect())

    def request(self, path, data=None, method=None, headers=None, client=None):
        payload = urllib.parse.urlencode(data).encode() if data is not None else None
        request = urllib.request.Request(self.base + path, data=payload, method=method, headers=headers or {})
        try:
            response = (client or self.client).open(request, timeout=5)
        except urllib.error.HTTPError as error:
            response = error
        with response:
            return response.status, response.headers, response.read().decode()

    def events(self):
        return json.loads((self.root / 'state.json').read_text())

    def forms(self, section='user', client=None):
        self.request('/__test/login', client=client)
        status, _, body = self.request(f'/khuljasimsim/{section}/index.php', client=client)
        self.assertEqual(status, 200)
        self.assertNotIn('Warning:', body)
        parser = Forms()
        parser.feed(body)
        self.assertTrue(parser.forms)
        self.assertTrue(all(form['method'] == 'post' for form in parser.forms))
        return parser.forms

    def test_get_head_and_options_never_mutate(self):
        self.forms()
        before = self.events()
        for path in ['/khuljasimsim/user/changeStatus.php?user_id=4242', '/khuljasimsim/property/remove.php?pid=99']:
            for method in ['GET', 'HEAD', 'OPTIONS']:
                status, headers, _ = self.request(path, method=method, headers={'Sec-Fetch-Site': 'cross-site'})
                self.assertEqual(status, 405)
                self.assertEqual(headers['Allow'], 'POST')
        self.assertEqual(self.events(), before)

    def test_post_requires_a_valid_token_before_loading_database(self):
        self.forms()
        before = self.events()
        for path, field, value in [('/khuljasimsim/user/changeStatus.php', 'user_id', '4242'),
                                   ('/khuljasimsim/property/remove.php', 'pid', '99')]:
            for extra in [{}, {'csrf_token': 'invalid'}, {'csrf_token[]': 'array'}]:
                status, _, _ = self.request(path, {field: value, **extra}, headers={'Origin': 'https://cross-site.invalid'})
                self.assertEqual(status, 403)
        self.assertEqual(self.events(), before)

    def test_post_without_a_form_session_fails_closed(self):
        self.request('/__test/login')
        status, _, _ = self.request('/khuljasimsim/user/changeStatus.php', {'user_id': '4242', 'csrf_token': 'arbitrary'})
        self.assertEqual(status, 403)
        self.assertEqual(self.events(), [])

    def test_token_is_bound_to_session_action_and_record(self):
        form = self.forms()[0]['data']
        other = self.new_client()
        self.forms(client=other)
        before = self.events()
        for path, data, client in [('/khuljasimsim/user/changeStatus.php', form, other),
                                    ('/khuljasimsim/user/changeStatus.php', {**form, 'user_id': '4243'}, self.client),
                                    ('/khuljasimsim/property/remove.php', {'pid': '99', 'csrf_token': form['csrf_token']}, self.client)]:
            status, _, _ = self.request(path, data, client=client)
            self.assertEqual(status, 403)
        self.assertEqual(self.events(), before)

    def test_malformed_ids_do_not_load_database(self):
        form = self.forms()[0]['data']
        before = self.events()
        for value in ['0', '-1', '1 OR 1=1', '1.5', '']:
            status, _, _ = self.request('/khuljasimsim/user/changeStatus.php', {**form, 'user_id': value})
            self.assertEqual(status, 400)
        status, _, _ = self.request('/khuljasimsim/user/changeStatus.php', {'user_id[]': '4242', 'csrf_token': form['csrf_token']})
        self.assertEqual(status, 400)
        self.assertEqual(self.events(), before)

    def test_valid_forms_mutate_exactly_once_and_redirect(self):
        for section, endpoint, operation, expected in [('user', 'changeStatus.php', 'user-status', 4242),
                                                       ('property', 'remove.php', 'property-status', 99)]:
            with self.subTest(section=section):
                form = self.forms(section)[0]['data']
                before = len(self.events())
                status, headers, _ = self.request(f'/khuljasimsim/{section}/{endpoint}', form)
                self.assertEqual(status, 303)
                self.assertEqual(headers['Location'], 'index.php')
                self.assertEqual(self.events()[before:], [{'operation': 'database', 'id': None}, {'operation': operation, 'id': expected}])

    def test_anonymous_post_does_not_load_database(self):
        for path, data in [('/khuljasimsim/user/changeStatus.php', {'user_id': '4242'}),
                           ('/khuljasimsim/property/remove.php', {'pid': '99'})]:
            status, _, _ = self.request(path, data)
            self.assertEqual(status, 302)
        self.assertEqual(self.events(), [])


if __name__ == '__main__':
    unittest.main()
